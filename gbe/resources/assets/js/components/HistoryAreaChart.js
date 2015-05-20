import React from 'react';
var rd3 = require('react-d3');
var AreaChart = rd3.AreaChart;

var datasetStore = require('../stores/DatasetStore');
var stateStore = require('../stores/StateStore');
var dataModelStore = require('../stores/DataModelStore');
var apiActions = require('../common/ApiActions');
var AccountTypes = require('../constants/AccountTypes');
var dispatcher = require('../common/BudgetAppDispatcher');
var ActionTypes = require('../constants/ActionTypes');
var datasetUtilities = require('../data/DatasetUtilities');
var Sparkline = require('react-sparkline');

var HistoryAreaChart = React.createClass({

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
            }
        };
    },

    prepareLocalState: function (dm) {
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
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
            var ids = this.props.componentData['mydatasets'].ids;
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

    componentWillReceiveProps: function () {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        this.prepareLocalState(dm);
    },

    shouldComponentUpdate: function (nextProps, nextState) {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
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

        return (
            <div className="form-group">
                <label>Select Level</label>
                <select className="form-control" onChange={this.onLevelChange} value={selectedLevel}>
                    {data.categories.map(function(item, index) {
                        return (
                            <option key={index} value={index}>{item}</option>
                        )
                    })}
                </select>
            </div>
        )
    },

    interactionPanel: function interactionPanel(data, rows) {
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        return (
            <div>
                <div className="row">
                    <div className="col-xs-4">
                        {this.renderLevelSelector(data, rows)}
                    </div>
                  <div className="col-xs-1"></div>
                  <div className="col-xs-4">
                      <div className="form-group">
                          <label>Select Account Type</label>
                          <select className="form-control" onChange={this.onAccountTypeChange} value={accountType}>
                              {
                                  this.props.accountTypes.map(
                                      function (type, index) {
                                          return <option key={index} value={type.value} > {type.name} </option>
                                      }
                                  )
                              }
                          </select>
                      </div>
                  </div>
                  <div className="col-xs-1"></div>
                  <div className="col-xs-2">
                      <button style={{float:"right"}} className="btn btn-primary" onClick={this.doReset}>Reset</button>
                  </div>
                </div>
            </div>
      )
    },

    prepareData: function computeChanges (inData) {
        let nYears = inData[0].amount.length;
        let length = inData.length;
        let outData = [];

        for (let year=0; year<nYears; ++year) {
            outData.push({
                name: inData.dataHeaders[year],
                values: []
            });
        }
        for (let i=0; i<length; ++i) {
            let item = inData[i];
            for (let year=0; year<nYears; ++year) {
                outData[year].values.push({
                    x: Number(inData.periods[year]),
                    y:item.amount[year]
                });
            }
        }
    },

    sortByAbsolutePercentage: function sortByAbsolutePercentage () {
        return item2.percentSort - item1.percentSort;
    },


    sortByAbsoluteDifference: function sortByAbsoluteDifference(item1, item2) {
        var result = Math.abs(item2.difference) - Math.abs(item1.difference);
        return result;
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
        let tdStyle={textAlign:"right"};
        return <tr key={index}>
            <td key="0" style={{width:"35%"}}>{label}</td>
            <td key="1">
                <Sparkline data={item.amount} />
            </td>
            {item.amount.map(function(item,index) {
                return <td key={index+2} style={tdStyle}>{datasetUtilities.formatDollarAmount(item)}</td>
            })}
        </tr>
    },

    render: function() {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        var selectedLevel = stateStore.getValue(this.props.storeId, 'selectedLevel');
        var newData = dm.getData({
            accountTypes:[accountType],
            startPath: [],
            nLevels: selectedLevel+1
        }, false);

        if (newData == null) {
            return (
                <div>
                    HistoryAreaChart is loading ...
                </div>
            )
        }
        else {
            var rows =  newData.data;
            var headers = newData.dataHeaders;
            let currentLevel = stateStore.getValue(this.props.storeId,'currentLevel');
            let dataLength = rows[0].amount.length;
            var chartData = prepareData(rows);
            //rows.map(this.computeChanges);
            //rows = rows.sort(this.sortByAbsoluteDifference);
            let thStyle={textAlign:"right"};
            return (
                <div>
                    <br/>
                    <hr/>
                    {this.interactionPanel(newData, rows)}
                    <br/>
                    <hr/>
                    <table className="table">
                        <thead>
                        <tr>
                            <th key="0">Category</th>
                            <th key="1">History<br/>{headers[0]}-{headers[dataLength-1]}</th>
                            {headers.map(function(item,index) {
                                return <th key={2+index} style={thStyle}>{item}</th>
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

export default HistoryAreaChart;
