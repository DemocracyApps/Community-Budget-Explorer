import React from 'react';

var datasetStore = require('../stores/DatasetStore');
var stateStore = require('../stores/StateStore');
var dataModelStore = require('../stores/DataModelStore');
var apiActions = require('../common/ApiActions');
var AccountTypes = require('../constants/AccountTypes');
var dispatcher = require('../common/BudgetAppDispatcher');
var ActionTypes = require('../constants/ActionTypes');

var MultiYearTable = React.createClass({

    propTypes: {
        componentData: React.PropTypes.object.isRequired,
        componentProps: React.PropTypes.object.isRequired,
        storeId: React.PropTypes.number.isRequired
    },

    // These should really be put in config store.
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
        console.log("MultiYear Table storeID = " + this.props.storeId + ", dataModelId = " + dataModelId);
        if (dataModelId == null) {
            var ids = this.props.componentData['alldata'].ids;
            ids.forEach(function (id) {
                apiActions.requestDatasetIfNeeded(id);
            });

            var dm = dataModelStore.createModel(ids, this.props.dataInitialization);
            stateStore.setComponentState(this.props.storeId,
                {
                    selectedItem: AccountTypes.REVENUE,
                    dataModelId: dm.id
                });
            console.log("Created MYT dataModelId: " + dm.id);
        }
    },

    shouldComponentUpdate: function (nextProps, nextState) {
        var dataModelId = stateStore.getComponentStateValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var selectedItem = stateStore.getComponentStateValue(this.props.storeId, 'selectedItem');
        console.log("In MYT shouldComponentUpdate returning "
            + ( dm.dataChanged() || dm.commandsChanged({accountTypes:[selectedItem]}) ));

        return ( dm.dataChanged() || dm.commandsChanged({accountTypes:[selectedItem]}) );
    },

    onSelectChange: function(e) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                name: 'selectedItem',
                value: Number(e.target.value)
            }
        });
    },

    dollarsWithCommas: function(x) {
        var prefix = '$';
        if (x < 0.) prefix = '-$';
        x = Math.abs(x);
        var val = prefix + x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        return val;
    },

    tableColumn: function (value, index) {
        return (
            <td key={index+1}>
                {this.dollarsWithCommas(value)}
            </td>
        )
    },

    tableRow: function (item, index) {
        return <tr key={index}>
            <td key="0">{item.categories[item.categories.length-1]} </td>
            {item.amount.map(this.tableColumn)}
        </tr>
    },

    columnHeader: function (header, index) {
        return <th key={index+1}>{header}</th>
    },

    render: function() {
        var dataModelId = stateStore.getComponentStateValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var selectedItem = stateStore.getComponentStateValue(this.props.storeId, 'selectedItem');
        console.log("MYT calling getData");
        var newData = dm.getData({accountTypes:[selectedItem]}, true);

        if (newData == null) {
            return <div> Multiyear table loading ...</div>
        }
        else {
            var rows = newData.data;
            var headers = newData.dataHeaders;

            return (
                <div>
                    <select onChange={this.onSelectChange} value={selectedItem}>
                        {
                            this.props.accountTypes.map(
                                function (type, index) {
                                    return <option key={index} value={type.value} > {type.name} </option>
                                }
                            )
                        }
                    </select>
                    <br/>
                    <table className="table">
                        <thead>
                            <tr>
                                <th key="0">Account</th>
                                {headers.map(this.columnHeader)}
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

export default MultiYearTable;
