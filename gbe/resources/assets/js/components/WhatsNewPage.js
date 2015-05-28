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

var WhatsNewPage = React.createClass({

    propTypes: {
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

            dm = dataModelStore.createModel(ids, this.props.dataInitialization);
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
                    subComponents: subComponents,
                    areaList: null,
                    selectedLevel: 1,
                    selectedArea: -1
                });
        }
    },

    shouldComponentUpdate: function (nextProps, nextState) {
        var areas = stateStore.getComponentStateValue(this.props.storeId, 'areaList');

        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var dataChanged = dm.dataChanged();
        if (areas == null) return true;

        var selectedLevel = stateStore.getValue(this.props.storeId, 'selectedLevel');
        var selectedArea = stateStore.getValue(this.props.storeId, 'selectedArea');
        var startPath = [];
        var addLevel = 1;
        if (areas != null && selectedArea >= 0) {
            startPath = [areas[selectedArea].name];
            addLevel = 0;
        }
        return ( dataChanged || dm.commandsChanged({startPath: startPath, nLevels: selectedLevel + addLevel}) );
    },

    onAccountTypeChange: function (type) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [{name: 'accountType', value: Number(type)}]
            }
        });
    },

    changeMode: function (e) {
        var currentMode = stateStore.getValue(this.props.storeId, 'displayMode');
        var displayMode = currentMode=="chart"?"table":"chart";

        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [{name: 'displayMode', value: displayMode}]
            }
        });
    },

    leftPanel: function leftPanel(displayMode) {
        if (displayMode == 'chart') {
            return (
                <div className="col-xs-4">
                </div>
            )
        }
        else {
            var spacer = String.fromCharCode(160)+String.fromCharCode(160)+String.fromCharCode(160);
            var yes="btn btn-xs btn-primary", no= "btn btn-xs btn-normal";
            var yesStyle={marginTop:4, marginBottom:2, color:"white"};
            var noStyle={color:"black", marginTop:4, marginBottom:2};

            var accountType = stateStore.getValue(this.props.storeId, 'accountType');
            return (
                <div className="col-xs-4">
                    <b style={{marginTop:4, fontSize:"small"}}>Account Type:</b>
                    <span>{spacer}</span>
                    <button style={(accountType==AccountTypes.EXPENSE)?yesStyle:noStyle}
                            className={(accountType==AccountTypes.EXPENSE)?yes:no}
                            onClick={this.onAccountTypeChange.bind(null, AccountTypes.EXPENSE)}>Spending</button>
                    <span>{spacer}</span>
                    <button style={(accountType==AccountTypes.REVENUE)?yesStyle:noStyle}
                            className={(accountType==AccountTypes.REVENUE)?yes:no}
                            onClick={this.onAccountTypeChange.bind(null, AccountTypes.REVENUE)}>Revenue</button>
                </div>
            )
        }
    },

    modeButtons: function() {
        var spacer = String.fromCharCode(160)+String.fromCharCode(160)+String.fromCharCode(160);
        var displayMode = stateStore.getValue(this.props.storeId, 'displayMode');
        var yes="btn btn-xs btn-primary", no= "btn btn-xs btn-normal";
        var yesStyle={marginTop:4, marginBottom:2, float:"right", color:"white"};
        var noStyle={float:"right", color:"black", marginTop:4, marginBottom:2};
        if (displayMode == 'chart') {
            return (
                <div className="col-xs-3">
                    <button style={noStyle} className={no}
                       onClick={this.changeMode}>Table View</button>
                    <span style={{float:"right"}}>{spacer}</span>
                    <button style={yesStyle} className={yes}
                       onClick={this.changeMode}>Chart View</button>
                </div>
            )
        }
        else {
            return (
                <div className="col-xs-3">
                    <button  style={yesStyle} className={yes}
                       onClick={this.changeMode}>Table View</button>
                    <span style={{float:"right"}}>{spacer}</span>
                    <button style={noStyle} className={no}
                       onClick={this.changeMode}>Chart View</button>
                </div>
            )
        }
    },

    detailLevel: function (which) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [{name: 'selectedLevel', value: Number(which)}]
            }
        });
    },

    middleButtons: function() {
        var level = stateStore.getValue(this.props.storeId, 'selectedLevel');
        var spacer = String.fromCharCode(160)+String.fromCharCode(160)+String.fromCharCode(160);
        var yes="btn btn-xs btn-primary", no= "btn btn-xs btn-normal";
        var yesStyle={marginTop:4, marginBottom:2, color:"white"}, noStyle={marginTop:4, marginBottom:2, color:"black"};
        return (
            <div className="col-xs-5">
                <b style={{marginTop:4, fontSize:"small"}}>Detail Level:</b>
                <span>{spacer}</span>
                <button style={(level==1)?yesStyle:noStyle} className={(level==1)?yes:no}
                   onClick={this.detailLevel.bind(null, 1)}>Department</button>
                <span>{spacer}</span>
                <button style={(level==2)?yesStyle:noStyle} className={(level==2)?yes:no}
                   onClick={this.detailLevel.bind(null, 2)}>Division</button>
                <span>{spacer}</span>
                <button style={(level==3)?yesStyle:noStyle} className={(level==3)?yes:no}
                   onClick={this.detailLevel.bind(null, 3)}>Account</button>
            </div>
        )
    },

    optionsPanel: function interactionPanel() {
        var displayMode = stateStore.getValue(this.props.storeId, 'displayMode');
        var selectedLevel = stateStore.getValue(this.props.storeId, 'selectedLevel');

        return (
            <div>
                <div className="row panel panel-default">
                    {this.leftPanel(displayMode)}
                    {this.middleButtons()}

                    {this.modeButtons()}
                </div>
            </div>
        )
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
            if (ahash.hasOwnProperty(nm)) {
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
        var areas = stateStore.getComponentStateValue(this.props.storeId, 'areaList');

        var selectedArea = stateStore.getValue(this.props.storeId, 'selectedArea');
        var startPath = [];
        var addLevel = 1;
        if (areas != null && selectedArea >= 0) {
            startPath = [areas[selectedArea].name];
            addLevel = 0;
        }
        var revenueData = dm.getData({
            accountTypes:[AccountTypes.REVENUE],
            startPath: startPath,
            nLevels: selectedLevel + addLevel
        });
        var expenseData = dm.getData({
            accountTypes:[AccountTypes.EXPENSE],
            startPath: startPath,
            nLevels: selectedLevel + addLevel
        }, false);
        var dataNull = (expenseData == null);

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
            while (revenueData.data.length <= 1 && expenseData.data.length <= 1 && selectedLevel < 3) {
                ++selectedLevel;
                revenueData = dm.getData({
                    accountTypes:[AccountTypes.REVENUE],
                    startPath: startPath,
                    nLevels: selectedLevel + addLevel
                });
                expenseData = dm.getData({
                    accountTypes:[AccountTypes.EXPENSE],
                    startPath: startPath,
                    nLevels: selectedLevel + addLevel
                }, false);
            }

            var rows = expenseData.data;
            if (areas == null) {
                areas = this.computeAreas(rows);
                stateStore.setComponentState(this.props.storeId, {areaList: areas});
            }
            rows.map(datasetUtilities.computeChanges);
            rows = rows.sort(datasetUtilities.sortByAbsoluteDifference).slice(0, 10);
            var topExpenses = [];
            for (let i = 0; i < rows.length; ++i) {
                let item = {
                    show: true,
                    name: rows[i].categories[selectedLevel],
                    categories: rows[i].categories.slice(0,selectedLevel+1),
                    value: rows[i].difference,
                    percent: rows[i].percent
                };
                topExpenses.push(item);
            }
            if (rows.length < 10) {
                for (let i=0; i<10-rows.length; ++i) {
                    topExpenses.push({
                        show: false,
                        name: "Filler+i",
                        categories: ["Filler"+i],
                        value: 0.0
                    });
                }
            }
            rows = revenueData.data;
            rows.map(datasetUtilities.computeChanges);
            rows = rows.sort(datasetUtilities.sortByAbsoluteDifference).slice(0, 10);
            var topRevenues = [];
            for (let i = 0; i < rows.length; ++i) {
                let item = {
                    show:true,
                    name: rows[i].categories[selectedLevel],
                    categories: rows[i].categories.slice(0,selectedLevel+1),
                    value: rows[i].difference,
                    percent: rows[i].percent
                };
                topRevenues.push(item);
            }
            if (rows.length < 10) {
                for (let i=0; i<10-rows.length; ++i) {
                    topRevenues.push({
                        show: false,
                        name: "Filler+i",
                        categories: ["Filler"+i],
                        value: 0.0
                    });
                }
            }

            return (
                <div className = "row">
                    <div className="col-xs-3">
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
                    <div className="col-xs-4">
                        <h2>Top Spending Changes</h2>
                        <VerticalBarChart width={350} height={600} data={topExpenses}/>
                    </div>
                    <div className="col-xs-1"></div>
                    <div className="col-xs-4">
                        <h2>Top Revenue Changes</h2>
                        <VerticalBarChart width={350} height={600}  data={topRevenues}/>
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

