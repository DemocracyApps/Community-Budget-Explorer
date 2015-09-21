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

var HistoryTable = React.createClass({

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
            componentMode: CommonConstants.STANDALONE_COMPONENT
        };
    },

    prepareLocalState: function (dm) {
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        var selectedLevel = this.props.selectedLevel;

        return dm.checkData({
            accountTypes: [accountType],
            startPath: [],
            nLevels: selectedLevel+1
        }, true);
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

            let reverseRevenueSign = false;
            if (this.props.site.properties.reverseRevenueSign) {
                reverseRevenueSign = true;
            }

            dm = dataModelStore.createModel(ids, {amountThreshold:0.01, reverseRevenueSign:reverseRevenueSign},
                this.props.site.categoryMap);

            stateStore.initializeComponentState(this.props.storeId,
                {
                    accountType: AccountTypes.EXPENSE,
                    dataModelId: dm.id,
                    selectedLevel: 1
                });
        }
    },

    componentWillReceiveProps: function () {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        this.prepareLocalState(dm);
    },

    shouldComponentUpdate: function (nextProps, nextState) {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        var result = ( dm.dataChanged() || dm.commandsChanged({accountTypes: [accountType]}) );
        return result;
    },

    onAccountTypeChange: function (e) {
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

    doReset: function (e) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [
                    {
                        name: 'selectedLevel',
                        value: 1
                    }
                ]
            }
        });
    },

    onLevelChange: function onLevelChange(e) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [
                    {
                        name: 'selectedLevel',
                        value: Number(e.target.value)
                    }
                ]
            }
        });
    },

    tableRow: function (item, index) {
        let label = item.categories[0];
        var selectedLevel = this.props.selectedLevel;
        if (selectedLevel > 0) {
            for (let i=1; i<=selectedLevel; ++i) {
                label += " " + String.fromCharCode(183) + " "+item.categories[i];
            }
        }

        // Note that Sparkline below can be replaced with MicroBarChart.
        let tdStyle={textAlign:"right"};
        return <tr key={index}>
            <td key="0" style={{width:"35%"}}>{label}</td>
            <td>
                <Sparkline data={item.amount} />
            </td>
            {item.amount.map(function(item, index) {
                return (
                    <td key={index+1} style={tdStyle}>{datasetUtilities.formatDollarAmount(item)}</td>
                )
            })}
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
            let dataLength = rows[0].amount.length;
            let thStyle={textAlign:"right"};

            return (
                <div>
                    <table className="table">
                        <thead>
                        <tr>
                            <th key="0">Category</th>
                            <th key="1">History<br/>{headers[0]}-{headers[dataLength-1]}</th>
                            {headers.map(function(item, index) {
                                return <th key={index+2} style={thStyle}>{item}</th>
                            })}
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

export default HistoryTable;
