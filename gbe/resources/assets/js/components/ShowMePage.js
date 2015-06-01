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
        let dm = null;
        if (dataModelId == null) {
            var ids = this.props.componentData['mydatasets'].ids;
            ids.forEach(function (id) {
                apiActions.requestDatasetIfNeeded(id);
            });

            dm = dataModelStore.createModel(ids, this.props.dataInitialization, this.props.site.categoryMap);
            let subComponents = {
                chart: {},
                table: {}
            };
            subComponents.chart.id = idGenerator.generateId();
            subComponents.chart.storeId = stateStore.registerComponent('components', subComponents.chart.id, {});
            configStore.registerComponent(subComponents.chart.storeId, 'components', subComponents.chart.id, {});

            subComponents.table.id = idGenerator.generateId();
            subComponents.table.storeId = stateStore.registerComponent('components', subComponents.table.id, {});
            configStore.registerComponent(subComponents.table.storeId, 'components', subComponents.table.id, {});

            stateStore.setComponentState(this.props.storeId,
                {
                    accountType: AccountTypes.EXPENSE,
                    dataModelId: dm.id,
                    displayMode: "chart",
                    selectedLevel: 1,
                    currentYear: -1,
                    subComponents: subComponents
                });

        }
    },

    shouldComponentUpdate: function (nextProps, nextState) {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var accountType = this.getAccountType;
        var status = ( dm.dataChanged() || dm.commandsChanged({accountTypes: [accountType]}) );
        return status;
    },


	// top options panel
    optionsPanel: function () {
        var displayMode = stateStore.getValue(this.props.storeId, 'displayMode');
        var detailPanel;
        if (displayMode == "chart") {
        	detailPanel = (<YearButtonPanel columns="6" storeId={this.props.storeId} />);
        }
        else {
        	detailPanel = (<DetailLevelButtonPanel columns="6" storeId={this.props.storeId} />);
        }

        return (
            <div>
                <hr style={{marginTop:10, marginBottom:10}}/>
                <div className="row ">
		        	<AccountTypeButtonPanel columns="3" storeId={this.props.storeId} />
        			<DisplayModeButtonPanel columns="3" storeId={this.props.storeId} />
        			{detailPanel}
        		</div>
                <hr style={{marginTop:10, marginBottom:10}}/>
            </div>
        )
    },

    renderCharts: function () {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        var currentYear = stateStore.getValue(this.props.storeId, 'currentYear');
        var newData = dm.getData({
            accountTypes:[accountType],
            startPath: [],
            nLevels: 4
        }, false);
        var dataNull = (newData == null);

        if (dataNull) {
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
            if (currentYear < 0) currentYear = newData.periods.length-1;
            return (
                <div>
                    <AvbTreemap width={1200} height={600}
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
        var displayMode = stateStore.getValue(this.props.storeId, 'displayMode');
        var renderFunction = (displayMode == "chart")?this.renderCharts:this.renderTable;

        return (
            <div>
                {this.optionsPanel()}
                {renderFunction()}
            </div>
        )
    }
});

export default ShowMePage;

