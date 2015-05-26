import React from 'react';
import ChangeExplorer from './ChangeExplorer';
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

    changeMode: function (e) {
        var currentMode = stateStore.getValue(this.props.storeId, 'displayMode');
        var displayMode = currentMode=="chart"?"table":"chart";

        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [
                    {
                        name: 'displayMode',
                        value: displayMode
                    }
                ]
            }
        });
    },

    optionsPanel: function interactionPanel() {
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        var displayMode = stateStore.getValue(this.props.storeId, 'displayMode');
        var modeButtonText = (displayMode == "chart")?"Table View":"Chart View";
        var selectLabelText = "Select Account Type:" + String.fromCharCode(160)+String.fromCharCode(160);
        return (
            <div>
                <div className="row">
                    <div className="col-xs-4">
                        <form className="form-inline">
                            <div className="form-group">
                                <label>{selectLabelText}<span width="30px"></span></label>
                                <select className="form-control" onChange={this.onAccountTypeChange} value={accountType}>
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
    },

    computeChanges: function computeChanges (item, index) {
        let length = item.amount.length;
        let useInfinity = false;
        if (length < 2) throw "Minimum of 2 datasets required for ChangeExplorer";
        let cur = item.amount[length-1], prev = item.amount[length-2];
        item.difference = cur-prev;
        if (Math.abs(prev) < 0.001) {
            if (useInfinity) {
                item.percent = String.fromCharCode(8734) + " %";
            }
            else {
                item.percent = "New";
            }
            item.percentSort = 10000 * Math.abs(item.difference);
        }
        else if (cur < 0. || prev < 0.) {
            item.percent="N/A";
            item.percentSort = 10000 * Math.abs(item.difference);
        }
        else {
            let pct = Math.round(1000*(item.difference)/prev)/10;
            item.percent = (pct) + "%";
            item.percentSort = Math.abs(item.percent);
        }
    },

    sortByAbsolutePercentage: function sortByAbsolutePercentage () {
        return item2.percentSort - item1.percentSort;
    },


    sortByAbsoluteDifference: function sortByAbsoluteDifference(item1, item2) {
        var result = Math.abs(item2.difference) - Math.abs(item1.difference);
        return result;
    },

    renderCharts: function () {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        console.log("Account type is now " + accountType);
        var newData = dm.getData({
            accountTypes:[accountType],
            startPath: [],
            nLevels: 4
        }, false);
        var dataNull = (newData == null);

        if (dataNull) {
            return (
                <div>
                    <p>Data is loading ... Please be patient</p>
                </div>
            )
        }
        else {
            var rows = newData.data;
            var headers = newData.dataHeaders;
            let dataLength = rows[0].amount.length;
            rows.map(this.computeChanges);
            rows = rows.sort(this.sortByAbsoluteDifference).slice(0, 10);

            var myData = [];
            for (let i = 0; i < rows.length; ++i) {
                let item = {
                    name: rows[i].categories[rows[i].categories.length - 1],
                    value: rows[i].difference
                };
                myData.push(item);
            }
            return (
                <div>
                    <AvbTreemap width={1200} height={600} data={newData}/>
                </div>
            )
        }
    },

    renderTable: function () {
        var subComponents = stateStore.getValue(this.props.storeId, 'subComponents');
        return (
            <div>
                <ChangeExplorer componentMode={CommonConstants.COMPOSED_COMPONENT}
                                datasetIds={this.props.componentData['mydatasets'].ids}
                                accountType={stateStore.getValue(this.props.storeId, 'accountType')}
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

