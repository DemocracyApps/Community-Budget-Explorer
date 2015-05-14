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

    componentWillMount: function () {
        // If this is the first time this component is mounting, we need to create the data model
        // and do any other state initialization required.
        var dataModelId = stateStore.getComponentStateValue(this.props.storeId, 'dataModelId');
        if (dataModelId == null) {
            var ids = this.props.componentData['mydatasets'].ids;
            ids.forEach(function (id) {
                apiActions.requestDatasetIfNeeded(id);
            });

            var dm = dataModelStore.createModel(ids, this.props.dataInitialization);
            stateStore.setComponentState(this.props.storeId,
                {
                    accountType: AccountTypes.REVENUE,
                    dataModelId:  dm.id,
                    currentLevel: 0
                });
        }
    },

    shouldComponentUpdate: function (nextProps, nextState) {
        var dataModelId = stateStore.getComponentStateValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var selectedItem = stateStore.getComponentStateValue(this.props.storeId, 'selectedItem');

        return ( dm.dataChanged() || dm.commandsChanged({accountTypes:[selectedItem]}) );
    },

    onSelectChange: function(e) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                name: 'accountType',
                value: Number(e.target.value)
            }
        });
    },

    interactionPanel: function interactionPanel(data, rows) {
        let currentLevel = stateStore.getValue(this.props.storeId,'currentLevel');
        return (
          <div className="row">
              <label>Select {data.categories[currentLevel]}</label>
              <select>
                  {rows.map(function(item, index) {
                     return (
                         <option key={index}>{item.categories[currentLevel]}</option>
                     )
                  })}
              </select>
          </div>
      )
    },

    tableRow: function (item, index) {
        return <tr key={index}>
            <td key="0">{item.categories[stateStore.getValue(this.props.storeId,'currentLevel')]}</td>
            <td key="1">{datasetUtilities.formatDollarAmount(item.reduce)}</td>
            <td key="2">{datasetUtilities.formatDollarAmount(item.amount[0])}</td>
            <td key="3">{datasetUtilities.formatDollarAmount(item.amount[1])}</td>
        </tr>
    },

    sortByAbsoluteDifference: function sortByAbsoluteDifference(item1, item2) {
        var result = Math.abs(item2.reduce) - Math.abs(item1.reduce);
        return result;
    },

    render: function() {
        var dataModelId = stateStore.getComponentStateValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var accountType = stateStore.getComponentStateValue(this.props.storeId, 'accountType');
        var newData = dm.getData({
            accountTypes:[accountType],
            startPath: [],
            nLevels: 1,
            reduce: this.props.componentProps.reduce
        }, true);
        if (newData == null) {
            return <div> BarchartExplorer loading ... </div>
        }
        else {
            var rows = newData.data.sort(this.sortByAbsoluteDifference);
            var headers = newData.dataHeaders;
            let currentLevel = stateStore.getValue(this.props.storeId,'currentLevel');
            //console.log("Got data with hierarchy " + newData.categories);
            //console.log("  LevelsDown = " + newData.levelsDown + ", levelsAggregated = " + newData.levelsAggregated);

            // So we'll display "Select {categories[levelsDown]}: " and a select with all the list (sorted and chopped)
            // Reset sets LevelsDown = 0
            // Select changes startPath.
            return (
                <div>
                    {this.interactionPanel(newData, rows)}
                    <br/>
                    <select onChange={this.onSelectChange} value={accountType}>
                        {
                            this.props.accountTypes.map(
                                function (type, index) {
                                    return <option key={index} value={type.value} > {type.name} </option>
                                }
                            )
                        }
                    </select>
                    <br/>
                    <hr/>
                    <table className="table">
                        <thead>
                            <tr>
                                <th key="0">Account</th>
                                <th key="1"> Delta </th>
                                <th key="2"> Value for {headers[0]} </th>
                                <th key="3"> Value for {headers[1]} </th>
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
