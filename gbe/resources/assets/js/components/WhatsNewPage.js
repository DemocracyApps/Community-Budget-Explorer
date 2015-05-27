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

    onAccountTypeChange: function (e) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [{name: 'accountType', value: Number(e.target.value)}]
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
        if (displayMode != 'chart') {
            var accountType = stateStore.getValue(this.props.storeId, 'accountType');
            var selectLabelText = "Account Type:" + String.fromCharCode(160)+String.fromCharCode(160);
            return (
                <form className="form-inline">
                    <div className="form-group">
                        <label>{selectLabelText}</label>
                        <select className="form-control" onChange={this.onAccountTypeChange} value={accountType}>
                            {this.props.accountTypes.map(function (type, index) {
                                        return <option key={index} value={type.value}> {type.name} </option>
                            })}
                        </select>
                    </div>
                </form>
            )
        }
    },

    detailLevel: function (e) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [{name: 'selectedLevel', value: Number(e.target.value)}]
            }
        });
    },

    optionsPanel: function interactionPanel() {
        var displayMode = stateStore.getValue(this.props.storeId, 'displayMode');
        var selectedLevel = stateStore.getValue(this.props.storeId, 'selectedLevel');
        var modeButtonText = (displayMode == "chart")?"Table View":"Chart View";
        var spacer = String.fromCharCode(160)+String.fromCharCode(160)+String.fromCharCode(160)+String.fromCharCode(160);
        return (
            <div>
                <div className="row panel panel-default">
                    <div className="col-xs-3">
                        {this.leftPanel(displayMode)}
                    </div>
                    <div className="col-xs-6">
                        <div className="form-group">
                            <form className="form-inline">
                                <label>Detail Level:</label>
                                <span>{spacer}</span>
                                <label className="radio-inline">
                                    <input value="1" className="radio-inline"
                                           checked={selectedLevel==1}
                                           name="detailLevel"
                                           type="radio" onChange={this.detailLevel}/> Department
                                </label>
                                <label className="radio-inline">
                                    <input value="2" className="radio-inline"
                                           checked={selectedLevel==2}
                                           name="detailLevel"
                                           type="radio" onChange={this.detailLevel}/> Division
                                </label>
                                <label className="radio-inline">
                                    <input value="3" className="radio-inline"
                                           checked={selectedLevel==3}
                                           name="detailLevel"
                                           type="radio" onChange={this.detailLevel}/> Account
                                </label>
                            </form>
                        </div>
                    </div>
                    <div className="col-xs-2">
                        <button style={{float:"right"}} className="btn btn-normal"
                                onClick={this.changeMode}>Switch To {modeButtonText}</button>
                    </div>
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
                    <br/>
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
                <br/>
                {this.optionsPanel()}
                <br/>
                {renderFunction()}
            </div>
        )
    }
});

export default WhatsNewPage;

