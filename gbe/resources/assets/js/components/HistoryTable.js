import React from 'react';

var datasetStore = require('../stores/DatasetStore');
var stateStore = require('../stores/StateStore');
var dataModelStore = require('../stores/DataModelStore');
var apiActions = require('../common/ApiActions');
var AccountTypes = require('../constants/AccountTypes');
var dispatcher = require('../common/BudgetAppDispatcher');
var ActionTypes = require('../constants/ActionTypes');
var datasetUtilities = require('../data/DatasetUtilities');
var CommonConstants = require('../constants/Common');

var Sparkline = require('react-sparkline');
var MicroBarChart = require('react-micro-bar-chart');

var HistoryTable = React.createClass({

    propTypes: {
        componentData: React.PropTypes.object.isRequired,
        componentProps: React.PropTypes.object.isRequired,
        storeId: React.PropTypes.number.isRequired
    },

    getDefaultProps: function () {
        return {
            accountTypes: [
                {name: "Expense", value: AccountTypes.EXPENSE},
                {name: "Revenue", value: AccountTypes.REVENUE}
            ],
            dataInitialization: {
                hierarchy: ['Fund', 'Department', 'Division', 'Account'],
                accountTypes: [AccountTypes.EXPENSE, AccountTypes.REVENUE],
                amountThreshold: 0.01
            },
            componentMode: CommonConstants.STANDALONE_COMPONENT
        };
    },

    getAccountType: function() {
        if (this.props.componentMode == CommonConstants.COMPOSED_COMPONENT) {
            return this.props.accountType;
        }
        else {
            return stateStore.getValue(this.props.storeId, 'accountType');
        }
    },

    prepareLocalState: function (dm) {
        var accountType = this.getAccountType();
        var selectedLevel = stateStore.getValue(this.props.storeId, 'selectedLevel');

        return dm.checkData({
            accountTypes: [accountType],
            startPath: [],
            nLevels: selectedLevel+1
        }, true);
    },

    componentWillMount: function () {
        // If this is the first time this component is mounting, we need to create the data model
        // and do any other state initialization required.
        var dataModelId = stateStore.getComponentStateValue(this.props.storeId, 'dataModelId');
        let dm = null;
        if (dataModelId == null) {
            var ids;
            if (this.props.hasOwnProperty('datasetIds')) {
                ids = this.props.datasetIds;
            }
            else {
                ids = this.props.componentData['mydatasets'].ids;
            }
            ids.forEach(function (id) {
                apiActions.requestDatasetIfNeeded(id);
            });

            dm = dataModelStore.createModel(ids, this.props.dataInitialization);
            stateStore.setComponentState(this.props.storeId,
                {
                    accountType: AccountTypes.EXPENSE,
                    dataModelId: dm.id,
                    selectedLevel: 0
                });
        }
    },

    componentWillUnmount: function () {
        console.log("ChangeExplorer will unmount");
        //var dataModelId = stateStore.getComponentStateValue(this.props.storeId, 'dataModelId');
        //if (dataModelId != null) dataModelStore.deleteModel(dataModelId);
    },

    componentWillReceiveProps: function () {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        this.prepareLocalState(dm);
    },

    shouldComponentUpdate: function (nextProps, nextState) {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var accountType = this.getAccountType;
        return ( dm.dataChanged() || dm.commandsChanged({accountTypes: [accountType]}) );
    },

    onAccountTypeChange: function (e) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [
                    {
                        name: 'accountType',
                        value: Number(e.target.value)
                    }
                ]
            }
        });
    },

    doReset: function (e) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [
                    {
                        name: 'selectedLevel',
                        value: 0
                    }
                ]
            }
        });
    },

    onLevelChange: function onLevelChange(e) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [
                    {
                        name: 'selectedLevel',
                        value: Number(e.target.value)
                    }
                ]
            }
        });
    },

    renderLevelSelector: function renderLevelSelector(data) {
        let selectedLevel = stateStore.getValue(this.props.storeId, 'selectedLevel');
        var selectLabelText = "Select Detail Level:" + String.fromCharCode(160)+String.fromCharCode(160);
        var spacer = String.fromCharCode(160)+String.fromCharCode(160)+String.fromCharCode(160)+String.fromCharCode(160);
        return (
            <div className="form-group">
                <form className="form-inline">
                    <label>{selectLabelText}</label>
                    <select className="form-control" onChange={this.onLevelChange} value={selectedLevel}>
                        {data.categories.map(function(item, index) {
                            return (
                                <option key={index} value={index}>{item}</option>
                            )
                        })}
                    </select>
                    <span>{spacer}</span>
                    <button className="btn btn-normal" onClick={this.doReset}>Reset</button>
                </form>
            </div>
        )
    },

    renderAccountSelector() {
        if (this.props.componentMode == CommonConstants.STANDALONE_COMPONENT) {
            return (
                <form className="form-inline">
                <div className="form-group">
                    <label>Select Account Type</label>
                    <select className="form-control" onChange={this.onAccountTypeChange} value={accountType}>
                        {
                            this.props.accountTypes.map(
                                function (type, index) {
                                    return <option key={index} value={type.value}> {type.name} </option>
                                }
                            )
                        }
                    </select>
                </div>
                </form>
            )
        }
    },

    interactionPanel: function interactionPanel(data, rows) {
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        return (
            <div>
                <div className="row">
                    <div className="col-xs-6">
                        {this.renderLevelSelector(data, rows)}
                    </div>
                  <div className="col-xs-6">
                      {this.renderAccountSelector()}
                  </div>
                </div>
            </div>
      )
    },

    tableRow: function (item, index) {
        let length = item.amount.length;
        let label = item.categories[0];
        var selectedLevel = stateStore.getValue(this.props.storeId, 'selectedLevel');
        if (selectedLevel > 0) {
            for (let i=1; i<=selectedLevel; ++i) {
                label += " " + String.fromCharCode(183) + " "+item.categories[i];
            }
        }

        // Note that Sparkline below can be replaced with MicroBarChart.
        let tdStyle={textAlign:"right"};
        return <tr key={index}>
            <td key="0" style={{width:"35%"}}>{label}</td>
            <td>
                <Sparkline data={item.amount} />
            </td>
            {item.amount.map(function(item, index) {
                return (
                    <td key={index+1} style={tdStyle}>{datasetUtilities.formatDollarAmount(item)}</td>
                )
            })}
        </tr>
    },

    render: function() {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var accountType = this.getAccountType();
        var selectedLevel = stateStore.getValue(this.props.storeId, 'selectedLevel');
        var newData = dm.getData({
            accountTypes:[accountType],
            startPath: [],
            nLevels: selectedLevel+1
        }, false);
        var dataNull = (newData == null);
        console.log("Rendering HistoryTable: dataModelId = " + dataModelId + ", dataNull = " + dataNull);

        if (newData == null) {
            return (
                <div>
                    <p>Data is loading ... Please be patient</p>
                </div>
            )
        }
        else {
            var rows =  newData.data;
            var headers = newData.dataHeaders;
            let currentLevel = stateStore.getValue(this.props.storeId,'currentLevel');
            let dataLength = rows[0].amount.length;
            rows.map(datasetUtilities.computeChanges);
            rows = rows.sort(datasetUtilities.sortByAbsoluteDifference);
            let thStyle={textAlign:"right"};
            return (
                <div>
                    {this.interactionPanel(newData, rows)}

                    <table className="table">
                        <thead>
                        <tr>
                            <th key="0">Category</th>
                            <th key="1">History<br/>{headers[0]}-{headers[dataLength-1]}</th>
                            {headers.map(function(item, index) {
                                return <th key={index+2} style={thStyle}>{item}</th>
                            })}
                        </tr>
                        </thead>
                        <tbody>
                        {rows.map(this.tableRow)}
                        </tbody>
                    </table>

                </div>
            )
        }
    }
});

export default HistoryTable;
