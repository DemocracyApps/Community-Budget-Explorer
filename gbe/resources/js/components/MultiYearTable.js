import React from 'react';

var datasetStore = require('../stores/DatasetStore');
var dataModelStore = require('../stores/DataModelStore');
var apiActions = require('../common/ApiActions');
var AccountTypeConstants = require('../constants/AccountTypeConstants');
var AccountTypes = AccountTypeConstants.AccountTypes;

var MultiYearTable = React.createClass({


    propTypes: {
        data: React.PropTypes.object.isRequired
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
                amountThreshold: 0.01,
                outputForm: 'array'
            }
        };
    },

    getInitialState: function() {
        return {
            selectedItem: AccountTypes.EXPENSE,
            timestamp: -1,
            dataModelId: null
        };
    },

    componentWillMount: function () {
        // Create the data model that we'll use
        var ids = this.props.data['alldata'].ids;
        ids.forEach(function(id) {
           apiActions.requestDatasetIfNeeded(id);
        });

        var dm = dataModelStore.createModel(ids, this.props.dataInitialization);
        this.setState ({
            dataModelId: dm.id,
            timestamp: dm.getTimestamp()
        });
    },

    componentDidMount: function () {
        dataModelStore.addChangeListener(this.onDataChange);
    },

    shouldComponentUpdate: function (nextProps, nextState) {
        var dataChanged = dataModelStore.getModel(this.state.dataModelId).dataChanged();
        console.log("Data change status = " + dataChanged);
        return (
            dataChanged ||
            this.state.timestamp !== nextState.timestamp ||
            this.state.selectedItem !== nextState.selectedItem
        );
    },

    onDataChange: function () {
        var dm = dataModelStore.getModel(this.state.dataModelId);
        dm.checkData();
        if (this.state.timestamp != dm.getTimestamp()) {
            this.setState({timestamp: dm.getTimestamp()});
        }
    },

    onChange: function(e) {
        this.setState({selectedItem: +e.target.value}); // Note cast to number
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
            <td>
                {this.dollarsWithCommas(value)}
            </td>
        )
    },

    tableRow: function (item, index) {
        return <tr key={index}>
            <td>{item.account} </td>
            {item.amount.map(this.tableColumn)}
        </tr>
    },

    columnHeader: function (header, index) {
        return <th>{header}</th>
    },

    render: function() {
        var dm = dataModelStore.getModel(this.state.dataModelId);

        var rows = dm.getData(
            {
                accountTypes:[this.state.selectedItem],
                outputForm: 'array'
            },
            true
            // Need to sort, maybe top N
        );
        if (rows == null) {
            return <div key={this.props.key}> Multiyear table loading ...</div>
        }
        else {
            console.log("Table rendering with row count " + rows.length);
            var headers = dm.getHeaders();
            return (
                <div key={this.props.key}>
                    <select onChange={this.onChange} value={this.state.selectedItem}>
                        {this.props.accountTypes.map(function (type, index)
                        {
                           return <option key={index} value={type.value} > {type.name} </option>
                        })}
                    </select>
                    <br/>
                    <table className="table">
                        <thead>
                            <tr>
                                <th>Account</th>
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
