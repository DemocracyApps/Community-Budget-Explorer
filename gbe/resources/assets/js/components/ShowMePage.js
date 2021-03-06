import React from 'react';
import HistoryTable from './HistoryTable';
import AvbTreemap from './AvbTreemap';


var datasetStore = require('../stores/DatasetStore');
var stateStore = require('../stores/StateStore');
var configStore = require('../stores/ConfigStore');
var dataModelStore = require('../stores/DataModelStore');
var apiActions = require('../common/ApiActions');
var idGenerator = require('../common/IdGenerator');
var AccountTypes = require('../constants/AccountTypes');
var dispatcher = require('../common/BudgetAppDispatcher');
var ActionTypes = require('../constants/ActionTypes');
var CommonConstants = require('../constants/Common');

var AccountTypeButtonPanel = require('./AccountTypeButtonPanel');
var DetailLevelButtonPanel = require('./DetailLevelButtonPanel');
var DisplayModeButtonPanel = require('./DisplayModeButtonPanel');
var YearButtonPanel = require('./YearButtonPanel');


var ShowMePage = React.createClass({

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
            ]
        };
    },

    initialize: function() {
        let dm = null;
        var ids = this.props.componentData['mydatasets'].ids;
        ids.forEach(function (id) {
            apiActions.requestDatasetIfNeeded(id);
        });
        let reverseRevenueSign = false;
        if (this.props.site.properties.reverseRevenueSign) {
            reverseRevenueSign = true;
        }

        dm = dataModelStore.createModel(ids, {amountThreshold:0.01, reverseRevenueSign:reverseRevenueSign},
            this.props.site.categoryMap);

        let subComponents = {
            chart: {},
            table: {}
        };
        // I don't like how we're handling year. Assumes that the Year selector defaults to last, not first.
        // Need to do this better.
        var currentYear = -1;
        if (this.props.componentProps.startYear == "0") {
            currentYear = 0;
        }

        stateStore.initializeComponentState(this.props.storeId,
            {
                accountType: AccountTypes.EXPENSE,
                dataModelId: dm.id,
                displayMode: "chart",
                selectedLevel: 1,
                currentYear: currentYear,
                subComponents: subComponents
            });

        subComponents.chart.storeId = stateStore.registerComponent(this.props.storeId, {});
        configStore.registerComponent(subComponents.chart.storeId, {});

        subComponents.table.storeId = stateStore.registerComponent(this.props.storeId, {});
        configStore.registerComponent(subComponents.table.storeId, {});

        stateStore.setOverrideValue(this.props.storeId, subComponents.chart.storeId, "accountType");
        stateStore.setOverrideValue(this.props.storeId, subComponents.chart.storeId, "selectedLevel");

        stateStore.setOverrideValue(this.props.storeId, subComponents.table.storeId, "accountType");
        stateStore.setOverrideValue(this.props.storeId, subComponents.table.storeId, "selectedLevel");
        return dm;
    },

    componentWillMount: function () {
        // If this is the first time this component is mounting, we need to create the data model
        // and do any other state initialization required.
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        if (dataModelId == null) {
            this.initialize();
        }
    },

    shouldComponentUpdate: function (nextProps, nextState) {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        var status = ( dm.dataChanged() || dm.commandsChanged({accountTypes: [accountType]}) );
        return status;
    },


	// top options panel
    optionsPanel: function (categories) {
        var displayMode = stateStore.getValue(this.props.storeId, 'displayMode');
        var detailPanel;
        if (displayMode == "chart") {
        	detailPanel = (<YearButtonPanel columns="6" storeId={this.props.storeId} />);
        }
        else if (this.props.componentProps.detailSelectorOn == "Yes") {
                detailPanel = (<DetailLevelButtonPanel columns="6" storeId={this.props.storeId} categories={categories}/>);
        }
        else {
            detailPanel = "";
        }

        if (this.props.componentProps.accountTypeSelectorOn == "Yes") {
            return (
                <div>
                    <hr style={{marginTop:10, marginBottom:10}}/>
                    <div className="row ">
                        <AccountTypeButtonPanel columns="3" storeId={this.props.storeId}/>
                        <DisplayModeButtonPanel columns="3" storeId={this.props.storeId}/>
                        {detailPanel}
                    </div>
                    <hr style={{marginTop:10, marginBottom:10}}/>
                </div>
            )
        }
        else {
            return (
                <div>
                    <hr style={{marginTop:10, marginBottom:10}}/>
                    <div className="row ">

                        <DisplayModeButtonPanel columns="3" storeId={this.props.storeId}/>
                        {detailPanel}
                    </div>
                    <hr style={{marginTop:10, marginBottom:10}}/>
                </div>
            )
        }
    },

    renderCharts: function () {
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        var currentYear = stateStore.getValue(this.props.storeId, 'currentYear');

        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);

        var newData = dm.getData({
            accountTypes:[accountType],
            startPath: [],
            nLevels: 4
        }, false);

        if (newData == null) {
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
            let width = 1200, height = 600;
            if (this.props.site.maxWidth) {
                width = Number(this.props.site.maxWidth);
                height = Math.trunc(height*width/1200);
            }
            if (currentYear < 0) {
                if (this.props.componentProps.startYear == "0") {
                    currentYear = 0;
                }
                else {
                    currentYear = newData.periods.length - 1;
                }
            }
            return (
                <div>
                    <AvbTreemap width={width} height={height}
                                data={newData}
                                year={newData.periods[currentYear]}
                                accountType={(accountType==AccountTypes.EXPENSE)?"Expenses":"Revenues"}/>
                </div>
            )
        }
    },

    renderTable: function () {
        var selectedLevel = stateStore.getValue(this.props.storeId, 'selectedLevel');
        var subComponents = stateStore.getValue(this.props.storeId, 'subComponents');
        return (
            <div>
                <HistoryTable componentMode={CommonConstants.COMPOSED_COMPONENT}
                              datasetIds={this.props.componentData['mydatasets'].ids}
                              accountType={stateStore.getValue(this.props.storeId, 'accountType')}
                              selectedLevel={selectedLevel}
                              storeId={subComponents.table.storeId}
                              site={this.props.site}
                              componentData={{}}
                              componentProps={{}}
                    />
            </div>
        )
    },

    render: function () {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm;

        if (dataModelId == null) {
            dm = this.initialize();
        }
        else {
            dm = dataModelStore.getModel(dataModelId);
        }
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        var displayMode = stateStore.getValue(this.props.storeId, 'displayMode');
        var renderFunction = (displayMode == "chart")?this.renderCharts:this.renderTable;

        var newData = dm.getData({
            accountTypes:[accountType],
            startPath: [],
            nLevels: 4
        }, false);
        var categories = null;
        if (newData != null) {
            categories = newData.categories.slice(1);
        }
        else {
            categories = ["Loading", "Loading", "Loading"];
        }

        return (
            <div>
                {this.optionsPanel(categories)}
                {renderFunction()}
                <br/>
                <br/>
            </div>
        )
    }
});

export default ShowMePage;

