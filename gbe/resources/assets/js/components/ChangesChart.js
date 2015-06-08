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

var ChangesChart = React.createClass({

    propTypes: {
        site: React.PropTypes.object.isRequired,
        storeId: React.PropTypes.number.isRequired,
        datasets: React.PropTypes.array.isRequired,
        accountType: React.PropTypes.number.isRequired,
        selectedLevel: React.PropTypes.number.isRequired
    },

    componentWillMount: function () {
        /*
         * If this is the first time this component is mounting, we need to
         *  1. Make sure the data has been requested via the API
         *  2. Create the data model (which merges datasets from multiple years & provides an interface)
         *  3. Initialize any state variables (here, just the ID of the data model)
         */
        let dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        if (dataModelId == null) {
            let ids = this.props.datasets;
            ids.forEach(function (id) { apiActions.requestDatasetIfNeeded(id); });

            let dm = dataModelStore.createModel(ids, {amountThreshold: 0.01}, this.props.site.categoryMap);
            stateStore.initializeComponentState(this.props.storeId, {dataModelId: dm.id});
        }
    },

    shouldComponentUpdate: function () {
        let dm = dataModelStore.getModel(stateStore.getValue(this.props.storeId, 'dataModelId'));
        return ( dm.dataChanged() || dm.commandsChanged({accountTypes: [this.props.accountType]}) );
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
        /*
         * The getData() method of a data model takes 2 arguments:
         *  1. A set of configuration parameters specifying what data to retrieve. Here, e.g. we
         *     specify which account types to include, where to start in the hierarchy, and how
         *     many levels to go down before starting to just aggregate. For example, to get a
         *     one-dimensional summary of expenses by department within General Government, we'd set
         *     accountTypes to [AccountTypes.EXPENSE], startPath to ['General Government'], and
         *     nLevels to 1.
         *  2. A boolean indicating whether we want a partial result (e.g., only 2 of 5 requested years
         *     have been received from the server). In some cases (e.g., a history table), we can render
         *     a useful result even with partial data. In this case, we need at least 2 datasets from
         *     consecutive years for a difference to make any sense, but we just keep it simple and wait
         *     until everything is received.
         */
        let dm = dataModelStore.getModel(stateStore.getValue(this.props.storeId, 'dataModelId'));
        let newData = dm.getData({
            accountTypes:[this.props.accountType],
            startPath: [],
            nLevels: this.props.selectedLevel+1
        }, false);

        if (newData == null) { // Data's not yet ready
            return (
                <div>
                    <p>Data is loading ... Please be patient</p>
                </div>
            )
        }
        else {
            let rows =  newData.data, headers = newData.dataHeaders;
            let dataLength = rows[0].amount.length;
            let thStyle={textAlign:"right"};
            let keyIndex = 0;

            // Set up the data by computing differences and sorting by them.
            rows.map(datasetUtilities.computeChanges);
            rows = rows.sort(datasetUtilities.sortByAbsoluteDifference);

            return (
                <div>
                    <table className="table">
                        <thead>
                            <tr>
                                <th key={keyIndex++}>Category</th>
                                <th key={keyIndex++}>History<br/>{headers[0]}-{headers[dataLength-1]}</th>
                                <th key={keyIndex++} style={thStyle}>{headers[dataLength-2]}</th>
                                <th key={keyIndex++} style={thStyle}>{headers[dataLength-1]}</th>
                                <th key={keyIndex++} style={thStyle}>Percentage<br/>Change</th>
                                <th key={keyIndex++} style={thStyle}>Actual<br/>Difference</th>
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

export default ChangesChart;
