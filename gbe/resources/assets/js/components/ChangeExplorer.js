import React from 'react';

var datasetStore = require('../stores/DatasetStore');
var stateStore = require('../stores/StateStore');
var dataModelStore = require('../stores/DataModelStore');
var apiActions = require('../common/ApiActions');
var AccountTypes = require('../constants/AccountTypes');
var dispatcher = require('../common/BudgetAppDispatcher');
var ActionTypes = require('../constants/ActionTypes');
var datasetUtilities = require('../data/DatasetUtilities');
var CommonConstants = require('../constants/Common');

var Sparkline = require('react-sparkline');
var MicroBarChart = require('react-micro-bar-chart');

var ChangeExplorer = React.createClass({

    propTypes: {
        site: React.PropTypes.object.isRequired,
        componentData: React.PropTypes.object.isRequired,
        componentProps: React.PropTypes.object.isRequired,
        storeId: React.PropTypes.number.isRequired
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
            },
            componentMode: CommonConstants.STANDALONE_COMPONENT
        };
    },

    componentWillMount: function () {
        // If this is the first time this component is mounting, we need to create the data model
        // and do any other state initialization required.
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        let dm = null;
        if (dataModelId == null) {
            var ids;
            if (this.props.hasOwnProperty('datasetIds')) {
                ids = this.props.datasetIds;
            }
            else {
                ids = this.props.componentData['mydatasets'].ids;
            }
            ids.forEach(function (id) {
                apiActions.requestDatasetIfNeeded(id);
            });

            dm = dataModelStore.createModel(ids, this.props.dataInitialization, this.props.site.categoryMap);
            stateStore.initializeComponentState(this.props.storeId,
                {
                    accountType: AccountTypes.EXPENSE,
                    dataModelId: dm.id,
                    selectedLevel: 0
                });
        }
    },

    shouldComponentUpdate: function (nextProps, nextState) {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var accountType = this.props.accountType;
        return ( dm.dataChanged() || dm.commandsChanged({accountTypes: [accountType]}) );
    },

    tableRow: function (item, index) {
        let length = item.amount.length;
        let label = item.categories[0];
        var selectedLevel = this.props.selectedLevel;
        if (selectedLevel > 0) {
            for (let i=1; i<=selectedLevel; ++i) {
                label += " " + String.fromCharCode(183) + " "+item.categories[i];
            }
        }

        let tdStyle={textAlign:"right"};
        return <tr key={index}>
            <td key="0" style={{width:"35%"}}>{label}</td>
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
        var accountType = this.props.accountType;
        var selectedLevel = this.props.selectedLevel;
        var newData = dm.getData({
            accountTypes:[accountType],
            startPath: [],
            nLevels: selectedLevel+1
        }, false);

        if (newData == null) {
            return (
                <div>
                    <p>Data is loading ... Please be patient</p>
                </div>
            )
        }
        else {
            var rows =  newData.data;
            var headers = newData.dataHeaders;
            let currentLevel = stateStore.getValue(this.props.storeId,'currentLevel');
            let dataLength = rows[0].amount.length;
            rows.map(datasetUtilities.computeChanges);
            rows = rows.sort(datasetUtilities.sortByAbsoluteDifference);
            let thStyle={textAlign:"right"};
            return (
                <div>
                    <table className="table">
                        <thead>
                        <tr>
                            <th key="0">Category</th>
                            <th key="1">History<br/>{headers[0]}-{headers[dataLength-1]}</th>
                            <th key="2" style={thStyle}>{headers[dataLength-2]}</th>
                            <th key="3" style={thStyle}>{headers[dataLength-1]}</th>
                            <th key="4" style={thStyle}>Percentage<br/>Change</th>
                            <th key="5" style={thStyle}>Actual<br/>Difference</th>
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
