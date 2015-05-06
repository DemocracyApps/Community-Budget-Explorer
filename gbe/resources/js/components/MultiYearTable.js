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
        stateId: React.PropTypes.number.isRequired
    },

    getDefaultProps: function() {
        return {
            accountTypes: [
                { name: "Expense", value: AccountTypes.EXPENSE},
                { name: "Revenue", value: AccountTypes.REVENUE}
            ],
            dataInitialization: {
                hierarchy: ['Fund', 'Department', 'Division'],
                accountTypes: [AccountTypes.EXPENSE, AccountTypes.REVENUE],
                amountThreshold: 0.01
            }
        };
    },

    getInitialState: function() {
        return {
            dataModelId: null
        };
    },

    componentWillMount: function () {
        // Create the data model that we'll use
        var ids = this.props.componentData['alldata'].ids;
        ids.forEach(function(id) {
           apiActions.requestDatasetIfNeeded(id);
        });

        var dm = dataModelStore.createModel(ids, this.props.dataInitialization);
        this.setState ({
            dataModelId: dm.id
        });
        stateStore.setComponentState(this.props.stateId, {selectedItem: AccountTypes.REVENUE});
    },

    shouldComponentUpdate: function (nextProps, nextState) {
        var dm = dataModelStore.getModel(this.state.dataModelId);
        var selectedItem = stateStore.getComponentStateValue(this.props.stateId, 'selectedItem');

        return ( dm.dataChanged() || dm.commandsChanged({accountTypes:[selectedItem]}) );
    },

    onSelectChange: function(e) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.stateId,
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
            <td key="0">{item.account} </td>
            {item.amount.map(this.tableColumn)}
        </tr>
    },

    columnHeader: function (header, index) {
        return <th key={index+1}>{header}</th>
    },

    render: function() {
        var dm = dataModelStore.getModel(this.state.dataModelId);
        var selectedItem = stateStore.getComponentStateValue(this.props.stateId, 'selectedItem');
        var rows = dm.getData({accountTypes:[selectedItem]}, true);

        if (rows == null) {
            return <div> Multiyear table loading ... </div>
        }
        else {
            var headers = dm.getHeaders();

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
