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

    categorySelector: function categorySelector(data, rows) {
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
        return (
          <div className="row">
              {this.categorySelector(data, rows)}
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
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        var startPath = stateStore.getValue(this.props.storeId, 'startPath');
        var newData = dm.getData({
            accountTypes:[accountType],
            startPath: startPath,
            nLevels: 1,
            reduce: this.props.componentProps.reduce
        }, true);

        if (newData == null) {
            return <div> BarchartExplorer loading ... </div>
        }
        else {
            var rows =  newData.data.sort(this.sortByAbsoluteDifference);
            var headers = newData.dataHeaders;
            let currentLevel = stateStore.getValue(this.props.storeId,'currentLevel');

            return (
                <div>
                    {this.interactionPanel(newData, rows)}
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
