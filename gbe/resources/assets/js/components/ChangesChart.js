import React from 'react';
import VerticalBarChart from './VerticalBarChart';

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

    /*
     * Initialization & lifecycle methods
     */
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
            let reverseRevenueSign = false;
            if (this.props.site.properties.reverseRevenueSign) {
                reverseRevenueSign = true;
            }

            let dm = dataModelStore.createModel(ids, {amountThreshold: 0.01, reverseRevenueSign:reverseRevenueSign}, this.props.site.categoryMap);
            console.log("Created model with id = " + dm.id);
            stateStore.initializeComponentState(this.props.storeId, {dataModelId: dm.id, selectedArea: -1});
        }
    },

    shouldComponentUpdate: function (nextProps, nextState) {
        let dm = dataModelStore.getModel(stateStore.getValue(nextProps.storeId, 'dataModelId'));
        let selectedArea = stateStore.getValue(nextProps.storeId, 'selectedArea');
        let acctType = nextProps.accountType;
        let startPath = [], addLevel = 1;
        dm.ensureDataReady();
        let areas = dm.getCategoryNames(null, 0, acctType);
        if (areas != null && selectedArea >= 0) {
            startPath = [areas[selectedArea].name];
            addLevel = 0;
        }
        let nLevels = nextProps.selectedLevel + addLevel;
        let commands = {accountTypes: [nextProps.accountType], startPath: startPath, nLevels: nLevels};
        return ( dm.dataChanged() || dm.commandsChanged(commands) );
    },

    /*
     * Computations
     */
    computeTopDifferences: function (currentData, selectedLevel) {
        let rows = currentData.data;
        rows.map(datasetUtilities.computeChanges);
        rows = rows.sort(datasetUtilities.sortByAbsoluteDifference).slice(0, 10);

        let topDifferences = [];
        for (let i = 0; i < rows.length; ++i) {
            let item = {
                show: true,
                name: rows[i].categories[selectedLevel],
                categories: rows[i].categories.slice(0,selectedLevel+1),
                value: rows[i].difference,
                percent: rows[i].percent,
                history: rows[i].amount
            };
            topDifferences.push(item);
        }
        if (rows.length < 10) {
            for (let i=0; i<10-rows.length; ++i) {
                topDifferences.push({
                    show: false,
                    name: "Filler+i",
                    categories: ["Filler"+i],
                    value: 0.0
                });
            }
        }
        return topDifferences;
    },

    /*
     * Rendering and interactivity methods (read from the bottom)
     */
    selectArea: function(e) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [{name: 'selectedArea', value: Number(e)}]
            }
        });
    },

    render: function() {
        let dm = dataModelStore.getModel(stateStore.getValue(this.props.storeId, 'dataModelId'));
        let selectedArea = stateStore.getValue(this.props.storeId, 'selectedArea');
        let acctType = this.props.accountType;
        let selectedLevel = this.props.selectedLevel;
        let startPath = [], L0 = 1;

        dm.ensureDataReady();
        let areas = dm.getCategoryNames(null, 0, acctType);
        if (areas != null && selectedArea >= 0) {
            startPath = [areas[selectedArea].name];
            L0 = 0;
        }

        let curData = dm.getData({accountTypes:[acctType], startPath: startPath, nLevels: selectedLevel + L0}, false);

        if (curData == null) {
            return (
                <div style={{height: 600}}>
                    <div className="row">
                        <div className="col-xs-3"></div>
                        <div className="col-xs-9">
                            <p>Data is loading ... Please be patient</p>
                        </div>
                    </div>
                </div>
            )
        }
        else {
            // Skip over any levels that have only a single item.
            while (curData.data.length <= 1 && selectedLevel < 3) {
                ++selectedLevel;
                curData = dm.getData({accountTypes:[acctType], startPath: startPath, nLevels: selectedLevel + L0}, false);
            }

            // Start by computing differences, sorting, and then slicing out the top 10.
            let topDifferences = this.computeTopDifferences(curData, selectedLevel);
            let w = Math.max(300,2* (100 * Math.trunc(window.innerWidth/100))/3);
            let txt = (this.props.accountType==AccountTypes.EXPENSE)?'Top Spending Changes':'Top Revenue Changes';
            return (
                <div className = "row">
                    <div className="col-md-3 col-sm-3">
                        <h2>Service Area</h2>
                        <br/>
                        <ul className="servicearea-selector nav nav-pills nav-stacked">
                            <li role="presentation" className={selectedArea==-1?"active":"not-active"}>
                                <a href="#" id={-1} onClick={this.selectArea}>All Areas</a>
                            </li>

                            {areas.map(function(item, index){
                                let spacer = String.fromCharCode(160);
                                return (
                                    <li role="presentation" className={selectedArea==index?"active":"not-active"}>
                                        <a href="#" id={index}
                                           onClick={this.selectArea.bind(null, index)}>{spacer} {item.name}</a>
                                    </li>
                                )
                            }.bind(this))}
                        </ul>
                    </div>
                    <div className="col-md-9 col-sm-9">
                        <h2>{txt}</h2>
                        <VerticalBarChart width={w} height={600} data={topDifferences}/>
                    </div>
                </div>
            )
        }
    }
});

export default ChangesChart;
