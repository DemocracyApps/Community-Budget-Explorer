import React from 'react';

var datasetStore = require('../stores/DatasetStore');
var stateStore = require('../stores/StateStore');
var dataModelStore = require('../stores/DataModelStore');
var apiActions = require('../common/ApiActions');
var AccountTypes = require('../constants/AccountTypes');
var dispatcher = require('../common/BudgetAppDispatcher');
var ActionTypes = require('../constants/ActionTypes');
var datasetUtilities = require('../data/DatasetUtilities');
var Sparkline = require('react-sparkline');

var ChangeExplorer = React.createClass({

    propTypes: {
        componentData: React.PropTypes.object.isRequired,
        componentProps: React.PropTypes.object.isRequired,
        storeId: React.PropTypes.number.isRequired
    },

    getInitialState: function () {
        return {showCategorySelector: false};
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
        var nLevels = stateStore.getValue(this.props.storeId, 'nLevels');
        var newData = dm.checkData({
            accountTypes: [accountType],
            startPath: [],
            nLevels: nLevels
        }, true);
        this.setState({showCategorySelector: false});
        if (newData != null) {
            let levelCount = newData.categories.length;
            let currentLevel = stateStore.getValue(this.props.storeId, 'currentLevel');
            if (currentLevel < levelCount - 1) {
                this.setState({showCategorySelector: true});
            }
        }
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
                    accountType: AccountTypes.REVENUE,
                    dataModelId: dm.id,
                    currentLevel: 0,
                    startPath: [],
                    nLevels: 1
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
        var selectedItem = stateStore.getValue(this.props.storeId, 'selectedItem');
        return ( dm.dataChanged() || dm.commandsChanged({accountTypes: [selectedItem]}) );
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

    onCategoryChange: function (e) {
        if (e.target.value != '--') {
            let startPath = stateStore.getValue(this.props.storeId, 'startPath');
            startPath.push(e.target.value);
            let currentLevel = stateStore.getValue(this.props.storeId, 'currentLevel');

            dispatcher.dispatch({
                actionType: ActionTypes.COMPONENT_STATE_CHANGE,
                payload: {
                    id: this.props.storeId,
                    changes: [
                        {
                            name: 'startPath',
                            value: startPath
                        },
                        {
                            name: 'currentLevel',
                            value: ++currentLevel
                        }
                    ]
                }
            });
        }
    },

    doReset: function (e) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [
                    {
                        name: 'startPath',
                        value: []
                    },
                    {
                        name: 'currentLevel',
                        value: 0
                    },
                    {
                        name: 'nLevels',
                        value: 1
                    }
                ]
            }
        });
    },

    renderCategorySelector: function categorySelector(data, rows) {
        if (this.state.showCategorySelector) {
            let currentLevel = stateStore.getValue(this.props.storeId, 'currentLevel');
            return (
                <div className="form-group">
                    <label>Restrict To Single {data.categories[currentLevel]}</label>
                    <select className="form-control" onChange={this.onCategoryChange} value="--">
                        <option key="0" value="--">--</option>
                        {rows.map(function (item, index) {
                            return (
                                <option key={index+1} value={item.categories[currentLevel]}>
                                    {item.categories[currentLevel]}
                                </option>
                            )
                        })}
                    </select>
                </div>
            )
        }
    },

    onLevelChange: function onLevelChange(e) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [
                    {
                        name: 'nLevels',
                        value: Number(e.target.value)
                    }
                ]
            }
        });
    },

    renderLevelSelector: function renderLevelSelector(data) {
        let nLevels = stateStore.getValue(this.props.storeId, 'nLevels');

        return (
            <div className="form-group">
                <label>Select Level</label>
                <select className="form-control" onChange={this.onLevelChange} value={nLevels}>
                    {data.categories.map(function(item, index) {
                        return (
                            <option key={index} value={index+1}>{item}</option>
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

                <div className="row">
                    <div className="col-xs-4">
                        {this.renderCategorySelector(data, rows)}
                    </div>
                    <div className="col-xs-8"></div>
                </div>
            </div>
      )
    },

    sortByAbsoluteDifference: function sortByAbsoluteDifference(item1, item2) {
        var result = Math.abs(item2.reduce) - Math.abs(item1.reduce);
        return result;
    },

    computeChanges: function computeChanges (item, index) {
        let length = item.amount.length;
        let useInfinity = false;
        if (length < 2) throw "Minimum of 2 datasets required for ChangeExplorer";
        let cur = item.amount[length-1], prev = item.amount[length-2];
        item.difference = cur-prev;
        if (Math.abs(prev) < 0.001) {
            if (useInfinity) {
                item.percent = String.fromCharCode(8734) + " %";
            }
            else {
                item.percent = "New";
            }
            item.percentSort = 10000 * Math.abs(item.difference);
        }
        else if (cur*prev < 0.) {
            item.percent="N/A";
            item.percentSort = 10000 * Math.abs(item.difference);
        }
        else {
            let pct = Math.round(1000*(item.difference)/prev)/10;
            item.percent = (pct) + "%";
            item.percentSort = Math.abs(item.percent);
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
        var nLevels = stateStore.getValue(this.props.storeId, 'nLevels');
        if (nLevels > 1) {
            for (let i=1; i<nLevels; ++i) {
                label += " " + String.fromCharCode(183) + " "+item.categories[i];
            }
        }
        let tdStyle={textAlign:"right"};
        return <tr key={index}>
            <td key="0" style={{width:"40%"}}>{label}</td>
            <td>
                <Sparkline data={item.amount} />
            </td>
            <td key="1" style={tdStyle}>{datasetUtilities.formatDollarAmount(item.amount[length-2])}</td>
            <td key="2" style={tdStyle}>{datasetUtilities.formatDollarAmount(item.amount[length-1])}</td>
            <td key="3" style={tdStyle}>{item.percent}</td>
            <td key="4" style={tdStyle}>{datasetUtilities.formatDollarAmount(item.difference)}</td>
        </tr>
    },

    render: function() {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        var startPath = stateStore.getValue(this.props.storeId, 'startPath');
        var nLevels = stateStore.getValue(this.props.storeId, 'nLevels');
        var newData = dm.getData({
            accountTypes:[accountType],
            startPath: startPath,
            nLevels: nLevels
        }, false);

        if (newData == null) {
            let tst = [17,2,33,4,10];
            return (
                <div>
                    ChangeExplorer is loading ...
                </div>
            )
        }
        else {
            var rows =  newData.data;
            var headers = newData.dataHeaders;
            let currentLevel = stateStore.getValue(this.props.storeId,'currentLevel');
            let dataLength = rows[0].amount.length;
            rows.map(this.computeChanges);
            rows = rows.sort(this.sortByAbsoluteDifference);
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
                            <th key="0">Account</th>
                            <th key="1">History</th>
                            <th key="2" style={thStyle}>{headers[dataLength-2]}</th>
                            <th key="3" style={thStyle}>{headers[dataLength-1]}</th>
                            <th key="4" style={thStyle}>Percentage Change</th>
                            <th key="5" style={thStyle}>Actual Difference</th>
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

export default ChangeExplorer;
