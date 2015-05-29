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
        var dataModelId = stateStore.getComponentStateValue(this.props.storeId, 'dataModelId');
        let dm = null;
        if (dataModelId == null) {
            var ids = this.props.componentData['mydatasets'].ids;
            ids.forEach(function (id) {
                apiActions.requestDatasetIfNeeded(id);
            });

            dm = dataModelStore.createModel(ids, this.props.dataInitialization, null);
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
        return ( dm.dataChanged() || dm.commandsChanged({accountTypes: [accountType]}) );
    },

    componentWillUnmount: function() {
        console.log("WhatsNewPage will unmount");
    },




	// top options panel
    optionsPanel: function () {
        var displayMode = stateStore.getValue(this.props.storeId, 'displayMode');
        var panels;
        if (displayMode == "chart") {
        	panels = [this.typePanel(3), this.modePanel(3), this.yearPanel(6), ];
        }
        else {
        	panels = [this.typePanel(3), this.modePanel(3), this.detailPanel(6)];
        }

        return (
            <div>
                <hr style={{marginTop:10, marginBottom:10}}/>
                <div className="row ">
                	{panels}
                </div>
                <hr style={{marginTop:10, marginBottom:10}}/>
            </div>
        )
    },
/*
    optionsPanel: function interactionPanel() {
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        var displayMode = stateStore.getValue(this.props.storeId, 'displayMode');
        var modeButtonText = (displayMode == "chart")?"Table View":"Chart View";
        var selectLabelText = "Select Account Type:" + String.fromCharCode(160)+String.fromCharCode(160);
        if (true) {
            return (
                <div>
                    <div className="row panel panel-default">
                        {this.leftPanel(displayMode)}
                        {this.middleButtons(displayMode)}
                        {this.modeButtons(displayMode)}
                    </div>
                </div>
            )
        }
        else {
            return (
                <div>
                    <div className="row">
                        <div className="col-xs-4">
                            <form className="form-inline">
                                <div className="form-group">
                                    <label>{selectLabelText}<span width="30px"></span></label>
                                    <select className="form-control" onChange={this.onAccountTypeChange}
                                            value={accountType}>
                                        {
                                            this.props.accountTypes.map(
                                                function (type, index) {
                                                    return <option key={index} value={type.value}> {type.name} </option>
                                                }
                                            )
                                        }
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div className="col-xs-6"></div>
                        <div className="col-xs-2">
                            <button style={{float:"right"}} className="btn btn-normal"
                                    onClick={this.changeMode}>Switch To {modeButtonText}</button>
                        </div>
                    </div>
                </div>
            )
        }
    },
*/
	// Generic button panel.
    buttonPanel: function(panelWidth, panelTitle, currentValue, setter, options) {
        return (
            <div className={"col-xs-"+panelWidth}>
                <div className="small"><strong>{panelTitle}:</strong></div>
                <div className="btn-group" role="group" aria-label={panelTitle}>
                	{options.map(function(option) {
			    		var callback = setter.bind(this, option.value);
			    		var className = "btn btn-default"
			    		if (option.value == null) {
			    			className += " disabled";
			    		} else if (currentValue === option.value) {
			    			className += " active";
			    		}
			    		return (<button className={className} onClick={callback}>{option.title}</button>)
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


	// year panel
    changeYear: function(value) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [{name: 'currentYear', value: value}]
            }
        });
    },

    yearPanel : function(panelWidth) {
		var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
		var accountType = stateStore.getValue(this.props.storeId, 'accountType');
		var currentYear = stateStore.getValue(this.props.storeId, 'currentYear');
		var dm = dataModelStore.getModel(dataModelId);
		var newData = dm.checkData({
			accountTypes:[accountType],
			startPath: [],
			nLevels: 4
		}, false);

		var options;
		if (newData == null) {
			options = [{title:"(loading...)"}];
		}
		else {
            if (currentYear < 0 && newData != null) currentYear = newData.periods.length-1;
			options = newData.periods.map(function(year, index) {
				return {value:index, title:year}
			});
		}
		return this.buttonPanel(panelWidth, "Year", currentYear, this.changeYear, options);
    },
/*
    middleButtons: function(displayMode) {
        if (displayMode == 'chart') {
            var spacer = String.fromCharCode(160) + String.fromCharCode(160) + String.fromCharCode(160);
            var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
            var dm = dataModelStore.getModel(dataModelId);
            var accountType = stateStore.getValue(this.props.storeId, 'accountType');
            var currentYear = stateStore.getValue(this.props.storeId, 'currentYear');
            var newData = dm.checkData({
                accountTypes:[accountType],
                startPath: [],
                nLevels: 4
            }, false);
            var headers = (newData==null)?['-']:newData.periods;
            if (currentYear < 0 && newData != null) currentYear = newData.periods.length-1;
            return (
                <div className="col-xs-5">
                    <form className="form-inline">
                        <div className="form-group">
                            <label style={{marginTop:4, fontSize:"small"}}>Year:<span>{spacer}</span></label>

                            <select style={{fontSize:"small"}} className="form-control" onChange={this.onYearChange} value={currentYear}>
                                {headers.map(function (item, index) {
                                    return (
                                        <option key={index} value={index}>{item}</option>
                                    )
                                })}
                            </select>
                        </div>
                    </form>
                </div>
            )
        }
        else {
            var level = stateStore.getValue(this.props.storeId, 'selectedLevel');
            var spacer = String.fromCharCode(160) + String.fromCharCode(160) + String.fromCharCode(160);
            var yes = "btn btn-xs btn-primary", no = "btn btn-xs btn-normal";
            var yesStyle = {marginTop: 4, marginBottom: 2, color: "white"}, noStyle = {
                marginTop: 4,
                marginBottom: 2,
                color: "black"
            };
            return (
                <div className="col-xs-5">
                    <b style={{marginTop:4, fontSize:"small"}}>Detail Level:</b>
                    <span>{spacer}</span>
                    <button style={(level==1)?yesStyle:noStyle} className={(level==1)?yes:no}
                            onClick={this.detailLevel.bind(null, 1)}>Department
                    </button>
                    <span>{spacer}</span>
                    <button style={(level==2)?yesStyle:noStyle} className={(level==2)?yes:no}
                            onClick={this.detailLevel.bind(null, 2)}>Division
                    </button>
                    <span>{spacer}</span>
                    <button style={(level==3)?yesStyle:noStyle} className={(level==3)?yes:no}
                            onClick={this.detailLevel.bind(null, 3)}>Account
                    </button>
                </div>
            )
        }
    },
*/
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

