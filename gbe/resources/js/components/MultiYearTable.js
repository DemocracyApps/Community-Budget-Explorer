import React from 'react';

var datasetStore = require('../stores/MainDatasetStore');
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
            version: -1,
            dataProvider: null
        };
    },

    componentWillMount: function () {
        this.state.dataProvider = datasetStore.getDataProvider(this.props.data['alldata'].id);
        this.state.dataProvider.setInitializer(this.props.dataInitialization);
        this.state.dataProvider.prepareData();
        this.setState({version: this.state.dataProvider.getVersion()})
    },

    componentDidMount: function () {
        datasetStore.addChangeListener(this._onDataChange);
        if (this.state.version != this.state.dataProvider.getVersion()) {
            this.setState({version: this.state.dataProvider.getVersion()});
        }
    },

    componentWillUnmount: function () {
        datasetStore.removeChangeListener(this._onDataChange);
    },

    _onDataChange: function () {
        if (this.state.version != this.state.dataProvider.getVersion()) {
            this.setState({version: this.state.dataProvider.getVersion()});
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

    tableRow: function (item, index) {
        return <tr key={index}>
            <td>{item.account}</td>
            <td>{this.dollarsWithCommas(item.amount[0])}</td>
            <td>{this.dollarsWithCommas(item.amount[1])}</td>
        </tr>
    },

    render: function() {
        var rows = this.state.dataProvider.getData(
            {
                accountTypes:[this.state.selectedItem],
                outputForm: 'array'
            }
            // Need to sort, maybe top N
        );

        if (rows == null) {
            return <div key={this.props.key}> Multiyear table loading ...</div>
        }
        else {
            console.log("Table rendering with row count " + rows.length);
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
                                <th>Account</th><th>2010</th><th>2014</th>
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
