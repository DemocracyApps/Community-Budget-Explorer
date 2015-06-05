import React from 'react';
import ChangeExplorer from './ChangeExplorer';
import VerticalBarChart from './VerticalBarChart';

var datasetStore = require('../stores/DatasetStore');
var stateStore = require('../stores/StateStore');
var configStore = require('../stores/ConfigStore');
var dataModelStore = require('../stores/DataModelStore');
var datasetUtilities = require('../data/DatasetUtilities');
var apiActions = require('../common/ApiActions');
var idGenerator = require('../common/IdGenerator');
var AccountTypes = require('../constants/AccountTypes');
var dispatcher = require('../common/BudgetAppDispatcher');
var ActionTypes = require('../constants/ActionTypes');
var CommonConstants = require('../constants/Common');

var AccountTypeButtonPanel = require('./AccountTypeButtonPanel');
var DetailLevelButtonPanel = require('./DetailLevelButtonPanel');
var DisplayModeButtonPanel = require('./DisplayModeButtonPanel');

var WhatsNewPage = React.createClass({

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
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
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

            stateStore.initializeComponentState(this.props.storeId,
                {
                    accountType: AccountTypes.EXPENSE,
                    dataModelId: dm.id,
                    displayMode: "chart",
                    subComponents: subComponents,
                    areaList: null,
                    selectedLevel: 1,
                    selectedArea: -1
                });

            subComponents.chart.storeId = stateStore.registerComponent(this.props.storeId, {});
            configStore.registerComponent(subComponents.chart.storeId, {});

            subComponents.table.storeId = stateStore.registerComponent(this.props.storeId, {});
            configStore.registerComponent(subComponents.table.storeId, {});

            stateStore.setOverrideValue(this.props.storeId, subComponents.chart.storeId, "accountType");
            stateStore.setOverrideValue(this.props.storeId, subComponents.chart.storeId, "selectedLevel");

            stateStore.setOverrideValue(this.props.storeId, subComponents.table.storeId, "accountType");
            stateStore.setOverrideValue(this.props.storeId, subComponents.table.storeId, "selectedLevel");
        }
    },

    shouldComponentUpdate: function (nextProps, nextState) {
        var areas = stateStore.getValue(this.props.storeId, 'areaList');

        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var dataChanged = dm.dataChanged();
        var areasNull = (areas == null);

        var selectedLevel = stateStore.getValue(this.props.storeId, 'selectedLevel');
        var selectedArea = stateStore.getValue(this.props.storeId, 'selectedArea');
        var startPath = [];
        var addLevel = 1;
        if (areas != null && selectedArea >= 0) {
            startPath = [areas[selectedArea].name];
            addLevel = 0;
        }
        let result = (areasNull || dataChanged || dm.commandsChanged({startPath: startPath, nLevels: selectedLevel + addLevel}) );
        return result;
    },

	// top options panel
    optionsPanel: function () {
        return (
            <div>
                <hr style={{marginTop:10, marginBottom:10}}/>
                <div className="row ">
                    <AccountTypeButtonPanel columns="3" storeId={this.props.storeId} />
                    <DisplayModeButtonPanel columns="3" storeId={this.props.storeId} />
                    <DetailLevelButtonPanel columns="6" storeId={this.props.storeId} />
                </div>
                <hr style={{marginTop:10, marginBottom:10}}/>
            </div>
        )
    },

	// Generic button panel.
    buttonPanel: function(panelWidth, panelTitle, currentValue, setter, options) {
        return (
            <div className={"col-xs-"+panelWidth}>
                <div className="small"><strong>{panelTitle}:</strong></div>
                <div className="btn-group" role="group" aria-label={panelTitle}>
                	{options.map(function(option) {
			    		var callback = setter.bind(this, option.value);
			    		return (<button className={"btn btn-default "+(currentValue === option.value ? "active" : "")}
								   onClick={callback}>{option.title}</button>)
			    	}, this)}
                </div>
            </div>)
    },

    // type panel
    changeAccountType: function (type) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [{name: 'accountType', value: Number(type)}]
            }
        });
    },

    typePanel: function (panelWidth) {
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        return this.buttonPanel(panelWidth, "Account Type", accountType, this.changeAccountType, [
        	{ value:AccountTypes.EXPENSE, title:"Spending" },
        	{ value:AccountTypes.REVENUE, title:"Revenue" },
        ]);
    },

    // mode panel
    changeMode: function (displayMode) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [{name: 'displayMode', value: displayMode}]
            }
        });
    },

    modePanel: function(panelWidth) {
        var displayMode = stateStore.getValue(this.props.storeId, 'displayMode');
        return this.buttonPanel(panelWidth, "Display", displayMode, this.changeMode, [
        	{ value:"chart", title:"Charts"},
        	{ value:"table", title:"Table"}
        ]);
 	},

	// detail panel
    changeDetailLevel: function (which) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [{name: 'selectedLevel', value: Number(which)}]
            }
        });
    },

    detailPanel: function(panelWidth) {
        var level = stateStore.getValue(this.props.storeId, 'selectedLevel');
        return this.buttonPanel(panelWidth, "Detail Level", level, this.changeDetailLevel, [
        	{ value:1, title:"Department"},
        	{ value:2, title:"Division"},
        	{ value:3, title:"Account"}
        ]);
    },


    computeAreas: function(rows) {
        var ahash = {};
        var nYears = rows[0].amount.length;
        for (let i=0; i<rows.length; ++i) {
            let current = ahash[rows[i].categories[0]];
            if (current == undefined) {
                current = {
                    name: rows[i].categories[0],
                    value: 0.0
                };
                ahash[current.name] = current;
            }
            current.value += rows[i].amount[nYears-1];
        }
        var areas = [];
        for (var nm in ahash) {
            if (ahash.hasOwnProperty(nm) && Math.abs(ahash[nm].value) > 0.0) {
                areas.push(ahash[nm]);
            }
        }
        areas = areas.sort(function(a, b) {
           return b.value - a.value;
        });
        return areas;
    },

    selectArea: function(e) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [{name: 'selectedArea', value: Number(e)}]
            }
        });
    },

    renderCharts: function () {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        var selectedLevel = stateStore.getValue(this.props.storeId, 'selectedLevel');
        var areas = stateStore.getValue(this.props.storeId, 'areaList');
        var selectedArea = stateStore.getValue(this.props.storeId, 'selectedArea');
        var startPath = [];
        var addLevel = 1;
        if (areas != null && selectedArea >= 0) {
            startPath = [areas[selectedArea].name];
            addLevel = 0;
        }
        var currentData = dm.getData({
            accountTypes:[accountType],
            startPath: startPath,
            nLevels: selectedLevel + addLevel
        }, false);
        var dataNull = (currentData == null);
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
            while (currentData.data.length <= 1 && selectedLevel < 3) {
                ++selectedLevel;
                currentData = dm.getData({
                    accountTypes:[AccountTypes.EXPENSE],
                    startPath: startPath,
                    nLevels: selectedLevel + addLevel
                }, false);
            }

            var rows = currentData.data;
            if (areas == null) {
                areas = this.computeAreas(rows);
                stateStore._setComponentState(this.props.storeId, {areaList: areas}); // Shouldn't be doing this, need to work out better way
            }
            rows.map(datasetUtilities.computeChanges);
            rows = rows.sort(datasetUtilities.sortByAbsoluteDifference).slice(0, 10);
            var topDifferences = [];
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

            var w = window.innerWidth;
            w = 100 * Math.trunc(w/100);

            var h = window.innerHeight;
            h = 100 * Math.round(h/100);
            if (h < 500) h = 500;
            w /= 12;
            w *= 8;
            if (w < 300) w = 300;
            var txt = (accountType==AccountTypes.EXPENSE)?'Top Spending Changes':'Top Revenue Changes';
            return (
                <div className = "row">
                    <div className="col-md-3 col-sm-3">
                        <h2>Service Area</h2>
                        <br/>
                        <ul className="servicearea-selector nav nav-pills nav-stacked">
                            <li role="presentation" className={selectedArea==-1?"active":"not-active"}><a href="#" id={-1} onClick={this.selectArea}>All Areas</a></li>
                            {areas.map(function(item, index){
                                var spacer = String.fromCharCode(160);
                                return <li role="presentation" className={selectedArea==index?"active":"not-active"}><a href="#" id={index}
                                              onClick={this.selectArea.bind(null, index)}>{spacer} {item.name}</a></li>
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
    },

    renderTable: function () {
        var subComponents = stateStore.getValue(this.props.storeId, 'subComponents');
        var selectedLevel = stateStore.getValue(this.props.storeId, 'selectedLevel');

        return (
            <div>
                <ChangeExplorer componentMode={CommonConstants.COMPOSED_COMPONENT}
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

export default WhatsNewPage;

