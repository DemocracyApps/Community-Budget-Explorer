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
                amountThreshold: 0.01
            },
            dataPrepCommands: [
                {
                    command: 'selectAccountTypes',
                    values: [AccountTypes.EXPENSE, AccountTypes.REVENUE]
                },
                {
                    command: 'setAmountThreshold',
                    value: 0.01,
                    abs: true
                },
                {
                    command: 'setHierarchy', // Primary immediate effect is to aggregate up all other hierarchy levels
                    fields: ['Fund', 'Department', 'Division']
                }
            ]
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
        this.setState({selectedItem: e.value});
    },

    render: function() {
        var rows = this.state.dataProvider.getData([
            {
                command: 'selectAccountTypes',
                values:[this.state.selectedItem]
            },
            {
                command: 'toArray'
            }
        ]);
        console.log("Table Rendering with rows = " + JSON.stringify(rows));

        if (rows == null) {
            return <div key={this.props.key}> Multiyear table loading ...</div>
        }
        else {
            return (
                <div key={this.props.key}>
                    <select onChange={this.onChange} value={this.state.selectedItem}>
                        {this.props.accountTypes.map(function (type, index)
                        {
                           return <option key={index} value={type.value} > {type.name} </option>
                        })}
                    </select>
                    <br/>
                    Got me some doggone data!
                </div>
            );
        }
    }
});

export default MultiYearTable;
