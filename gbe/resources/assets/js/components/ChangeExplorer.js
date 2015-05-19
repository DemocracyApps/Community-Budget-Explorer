import React from 'react';

var datasetStore = require('../stores/DatasetStore');
var stateStore = require('../stores/StateStore');
var dataModelStore = require('../stores/DataModelStore');
var apiActions = require('../common/ApiActions');
var AccountTypes = require('../constants/AccountTypes');
var dispatcher = require('../common/BudgetAppDispatcher');
var ActionTypes = require('../constants/ActionTypes');
var datasetUtilities = require('../data/DatasetUtilities');

var ChangeExplorer = React.createClass({

    propTypes: {
        componentData: React.PropTypes.object.isRequired,
        componentProps: React.PropTypes.object.isRequired,
        storeId: React.PropTypes.number.isRequired
    },

    getInitialState: function() {
        return {showCategorySelector:false};
    },

    getDefaultProps: function() {
        return {
            accountTypes: [
                { name: "Expense", value: AccountTypes.EXPENSE},
                { name: "Revenue", value: AccountTypes.REVENUE}
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
        var newData = dm.checkData({
            accountTypes:[accountType],
            startPath: [],
            nLevels: 1
        }, true);
        this.setState({showCategorySelector:false});
        if (newData != null) {
            let nLevels = newData.categories.length;
            let currentLevel = stateStore.getValue(this.props.storeId,'currentLevel');
            if (currentLevel < nLevels-1) {
                this.setState({showCategorySelector:true});
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
                    dataModelId:  dm.id,
                    currentLevel: 0,
                    startPath: []
                });
        }
    },

    componentWillReceiveProps: function() {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        this.prepareLocalState(dm);
    },

    shouldComponentUpdate: function (nextProps, nextState) {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var selectedItem = stateStore.getValue(this.props.storeId, 'selectedItem');
        return ( dm.dataChanged() || dm.commandsChanged({accountTypes:[selectedItem]}) );
    },

    onAccountTypeChange: function(e) {
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

    onCategoryChange: function(e) {
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
                    }
                ]
            }
        });
    },

    renderCategorySelector: function categorySelector(data, rows) {
        if (this.state.showCategorySelector) {
            let currentLevel = stateStore.getValue(this.props.storeId,'currentLevel');
            return (
                <div className="form-group">
                    <label>Select {data.categories[currentLevel]}</label>
                    <select className="form-control" onChange={this.onCategoryChange} value="--">
                        <option key="0" value="--">--</option>
                        {rows.map(function(item, index) {
                            return (
                                <option key={index+1} value={item.categories[currentLevel]}>{item.categories[currentLevel]}</option>
                            )
                        })}
                    </select>
                </div>
            );
        }
    },

    interactionPanel: function interactionPanel(data, rows) {
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        return (
          <div className="row">
              <div className="col-xs-6">
                  {this.renderCategorySelector(data, rows)}
              </div>
              <div className="col-xs-1"></div>
              <div className="col-xs-3">
                  <br/>
                  <select onChange={this.onAccountTypeChange} value={accountType}>
                      {
                          this.props.accountTypes.map(
                              function (type, index) {
                                  return <option key={index} value={type.value} > {type.name} </option>
                              }
                          )
                      }
                  </select>
              </div>
              <div className="col-xs-1"></div>
              <div className="col-xs-2">
                  <br/>
                  <button className="btn" onClick={this.doReset}>Reset</button>
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
        if (length < 2) throw "Minimum of 2 datasets required for ChangeExplorer";
        let cur = item.amount[length-1], prev = item.amount[length-2];
        item.difference = cur-prev;
        if (Math.abs(prev) < 0.001) {
            item.percent = String.fromCharCode(8734);
            item.percentSort = 10000 * Math.abs(item.difference);
        }
        else if (cur*prev < 0.) {
            item.percent="N/A";
            item.percentSort = 10000 * Math.abs(item.difference);
        }
        else {
            let pct = Math.round(10000*(item.difference)/prev)/100;
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
        let currentLevel = stateStore.getValue(this.props.storeId,'currentLevel');
        let label = item.categories[0];
        if (currentLevel > 0) {
            for (let i=1; i<=currentLevel; ++i) {
                label += ">"+item.categories[i];
            }
        }
        return <tr key={index}>
            <td key="0">{label}</td>
            <td key="1">{datasetUtilities.formatDollarAmount(item.amount[length-2])}</td>
            <td key="2">{datasetUtilities.formatDollarAmount(item.amount[length-1])}</td>
            <td key="3">{item.percent}</td>
            <td key="4">{datasetUtilities.formatDollarAmount(item.difference)}</td>
        </tr>
    },

    //<th key="0">Account</th>
    //<th key="1">{headers[dataLength-2]}</th>
    //<th key="2">{headers[dataLength-1]}</th>
    //<th key="3">Percentage Change</th>
    //<th key="4">Actual Difference</th>

    render: function() {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        var startPath = stateStore.getValue(this.props.storeId, 'startPath');
        var newData = dm.getData({
            accountTypes:[accountType],
            startPath: startPath,
            nLevels: 1
        }, false);

        if (newData == null) {
            return <div> ChangeExplorer is loading ... </div>
        }
        else {
            var rows =  newData.data;
            var headers = newData.dataHeaders;
            let currentLevel = stateStore.getValue(this.props.storeId,'currentLevel');
            let dataLength = rows[0].amount.length;

            rows.map(this.computeChanges);
            rows = rows.sort(this.sortByAbsoluteDifference);
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
                            <th key="1">{headers[dataLength-2]}</th>
                            <th key="2">{headers[dataLength-1]}</th>
                            <th key="3">Percentage Change</th>
                            <th key="4">Actual Difference</th>
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
