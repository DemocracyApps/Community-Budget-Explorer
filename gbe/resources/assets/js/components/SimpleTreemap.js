import React from 'react';
var rd3 = require('react-d3');
var Treemap = rd3.Treemap;

var datasetStore = require('../stores/DatasetStore');
var stateStore = require('../stores/StateStore');
var dataModelStore = require('../stores/DataModelStore');
var apiActions = require('../common/ApiActions');
var AccountTypes = require('../constants/AccountTypes');
var dispatcher = require('../common/BudgetAppDispatcher');
var ActionTypes = require('../constants/ActionTypes');
var datasetUtilities = require('../data/DatasetUtilities');

var SimpleTreemap = React.createClass({

    propTypes: {
        componentData: React.PropTypes.object.isRequired,
        componentProps: React.PropTypes.object.isRequired,
        storeId: React.PropTypes.number.isRequired
    },

    getDefaultProps: function() {
        return {
            accountTypes: [
                { name: "Expense", value: AccountTypes.EXPENSE},
                { name: "Revenue", value: AccountTypes.REVENUE}
            ],
            dataInitialization: {
                hierarchy: ['Fund', 'Department', 'Division', 'Account'],
                accountTypes: [AccountTypes.EXPENSE, AccountTypes.REVENUE],
                amountThreshold: 0.01
            }
        };
    },

    getInitialState: function() {
        return {componentWidth:750};
    },

    prepareLocalState: function (dm) {
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        var newData = dm.checkData({
            accountTypes:[accountType],
            startPath: [],
            nLevels: 1
        }, true);
        return newData;
    },

    componentDidMount: function () {
        var widthDelta = Math.abs(this.getDOMNode().offsetWidth - this.state.componentWidth);
        if (widthDelta > 10) this.setState({componentWidth:this.getDOMNode().offsetWidth});
    },

    componentDidUpdate: function() {
        var widthDelta = Math.abs(this.getDOMNode().offsetWidth - this.state.componentWidth);
        if (widthDelta > 10) this.setState({componentWidth:this.getDOMNode().offsetWidth});
    },

    componentWillMount: function () {
        // If this is the first time this component is mounting, we need to create the data model
        // and do any other state initialization required.
        var dataModelId = stateStore.getComponentStateValue(this.props.storeId, 'dataModelId');
        let dm = null;
        if (dataModelId == null) {
            var ids = this.props.componentData['mydataset'].ids;
            ids.forEach(function (id) {
                apiActions.requestDatasetIfNeeded(id);
            });

            dm = dataModelStore.createModel(ids, this.props.dataInitialization);
            stateStore.setComponentState(this.props.storeId,
                {
                    accountType: AccountTypes.EXPENSE,
                    dataModelId:  dm.id,
                    currentLevel: 0,
                    startPath: []
                });
        }
    },

    componentWillReceiveProps: function() {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        this.prepareLocalState(dm);
    },

    shouldComponentUpdate: function (nextProps, nextState) {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var selectedItem = stateStore.getValue(this.props.storeId, 'selectedItem');
        return ( dm.dataChanged() || dm.commandsChanged({accountTypes:[selectedItem]}) );
    },

    clickHandler: function (context) {
        //alert("Yo! Index = " + context.index + ", label = " + context.label + ", value = " + context.value);
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var newData = this.prepareLocalState(dm);

        let currentLevel = stateStore.getValue(this.props.storeId, 'currentLevel');
        let nLevels = newData.categories.length;
        if (currentLevel < nLevels-1) {
            let startPath = stateStore.getValue(this.props.storeId, 'startPath');
            startPath.push(context.label);
            dispatcher.dispatch({
                actionType: ActionTypes.COMPONENT_STATE_CHANGE,
                payload: {
                    id: this.props.storeId,
                    changes: [
                        {
                            name: 'startPath',
                            value: startPath
                        },
                        {
                            name: 'currentLevel',
                            value: ++currentLevel
                        }
                    ]
                }
            });
        }
    },

    doReset: function (e) {
        dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [
                    {
                        name: 'startPath',
                        value: []
                    },
                    {
                        name: 'currentLevel',
                        value: 0
                    }
                ]
            }
        });
    },

    onAccountTypeChange: function(e) {
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

    render: function() {
        var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
        var dm = dataModelStore.getModel(dataModelId);
        var accountType = stateStore.getValue(this.props.storeId, 'accountType');
        var startPath = stateStore.getValue(this.props.storeId, 'startPath');
        var newData = dm.getData({
            accountTypes:[accountType],
            startPath: startPath,
            nLevels: 1,
        }, false);
        let currentLevel = stateStore.getValue(this.props.storeId,'currentLevel');


        var treemapData = [];
        if (newData != null) {
            for (let i = 0; i < newData.data.length; ++i) {
                let item = newData.data[i];
                let name = item.categories[currentLevel];
                treemapData.push({label:name, value:item.amount[0]});
            }
        }
        //var treemapData = [
        //    {label: "China", value: 1364},
        //    {label: "India", value: 1296},
        //    {label: "Brazil", value: 703},
        //    {label: "Indonesia", value: 303},
        //    {label: "United States", value: 203}
        //];

        var title = (currentLevel==0)?"All Funds":"";
        if (currentLevel > 0) {
            for (let i=0; i<startPath.length; ++i) {
                if (i==0) title = startPath[0];
                else {
                    title += " > " + startPath[i];
                }
            }
        }

        return (
            <div >
                <div className="row">
                    <div className="col-xs-7">
                        <br/>
                        <select onChange={this.onAccountTypeChange} value={accountType}>
                            {
                                this.props.accountTypes.map(
                                    function (type, index) {
                                        return <option key={index} value={type.value} > {type.name} </option>
                                    }
                                )
                            }
                        </select>
                    </div>
                    <div className="col-xs-2"></div>
                    <div className="col-xs-3">
                        <br/>
                        <button className="btn" onClick={this.doReset}>Reset</button>
                    </div>
                </div>
                <div className="row">

                    <Treemap
                        data={treemapData}
                        width={this.state.componentWidth}
                        height={this.state.componentWidth}
                        textColor="#484848"
                        fontSize="10px"
                        title={title}
                        hoverAnimation={true}
                        eventHandlers={{onClick: this.clickHandler}}
                        extraProperties={{avgCharWidth: 5}}
                        />
                </div>
            </div>
        )
    }
});

export default SimpleTreemap;
