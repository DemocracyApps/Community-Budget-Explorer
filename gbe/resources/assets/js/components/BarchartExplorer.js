import React from 'react';

var datasetStore = require('../stores/DatasetStore');
var stateStore = require('../stores/StateStore');
var dataModelStore = require('../stores/DataModelStore');
var apiActions = require('../common/ApiActions');
var AccountTypes = require('../constants/AccountTypes');
var dispatcher = require('../common/BudgetAppDispatcher');
var ActionTypes = require('../constants/ActionTypes');
var datasetUtilities = require('../data/DatasetUtilities');

var BarchartExplorer = React.createClass({

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
            nLevels: 1,
            reduce: this.props.componentProps.reduce
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

    tableRow: function (item, index) {
        return <tr key={index}>
            <td key="0">{item.categories[stateStore.getValue(this.props.storeId,'currentLevel')]}</td>
            <td key="2">{datasetUtilities.formatDollarAmount(item.amount[0])}</td>
            <td key="3">{datasetUtilities.formatDollarAmount(item.amount[1])}</td>
            <td key="1">{datasetUtilities.formatDollarAmount(item.reduce)}</td>
        </tr>
    },

    sortByAbsoluteDifference: function sortByAbsoluteDifference(item1, item2) {
        var result = Math.abs(item2.reduce) - Math.abs(item1.reduce);
        return result;
    },

    bars: function (item, index) {
            return (
                <g key={index} transform={"translate(" + item.x1 +"," + item.y+")"}>
                    <rect strokeWidth="2" height="19" width={item.width}></rect>
                </g>
            )
    },

    render: function() {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        var startPath = stateStore.getValue(this.props.storeId, 'startPath');
        var newData = dm.getData({
            accountTypes:[accountType],
            startPath: startPath,
            nLevels: 1,
            reduce: this.props.componentProps.reduce
        }, false);

        if (newData == null) {
            return <div> BarchartExplorer loading ... </div>
        }
        else {
            var rows =  newData.data.sort(this.sortByAbsoluteDifference);
            var headers = newData.dataHeaders;
            let currentLevel = stateStore.getValue(this.props.storeId,'currentLevel');

            let chartWidth = 700, chartHeight = 500; // We need to get these from enclosing div - how do we do that?

            let minValue = 1.e7, maxValue = -1.e7;
            for (let i=0; i< rows.length; ++i) {
                minValue = Math.min(minValue, rows[i].reduce);
                maxValue = Math.max(maxValue, rows[i].reduce);
            }
            if (minValue > 0.0) minValue = 0.0;
            if (maxValue < 0.0) maxValue = 0.0;

            let xborder = 150, yborder = 5;
            let offset = -minValue;
            let scale = (chartWidth - 2 * xborder)/(maxValue - minValue);
            console.log("offset = " + offset + ",  scale = " + scale);
            for (let i=0; i< rows.length; ++i) {
                rows[i].x = Math.round(scale * (rows[i].reduce + offset)) + xborder;
                if (rows[i].reduce < 0) {
                    rows[i].x1 = Math.round(scale * (rows[i].reduce + offset)) + xborder;
                    rows[i].x2 = Math.round(scale * (0+offset)) + xborder;
                }
                else {
                    rows[i].x1 = Math.round(scale * (0+offset)) + xborder;
                    rows[i].x2 = Math.round(scale * (rows[i].reduce + offset)) + xborder;
                }
                rows[i].y = yborder + i * 40 + 10;
                rows[i].width = rows[i].x2 - rows[i].x1;
                console.log("Reduce=" + Math.round(rows[i].reduce) + ", x1/x2 = " + rows[i].x1 + "/" + rows[i].x2 + ", width = " + rows[i].width);
            }

            return (
                <div>
                    {this.interactionPanel(newData, rows)}
                    <br/>
                    <div>
                        <svg className="chart span12" id="chart" width="700" height="470">
                            {rows.map(this.bars)}
                        </svg>
                    </div>
                    <br/>
                    <hr/>
                    <table className="table">
                        <thead>
                        <tr>
                            <th key="0">Account</th>
                            <th key="2">{headers[0]}</th>
                            <th key="3">{headers[1]}</th>
                            <th key="1">Difference</th>
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

export default BarchartExplorer;
