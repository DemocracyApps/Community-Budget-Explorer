import React from 'react';
import ChangesTable from './ChangesTable';
import ChangesChart from './ChangesChart';

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

    /*
     * Initialization & lifecycle methods
     */
    propTypes: {
        site: React.PropTypes.object.isRequired,
        componentData: React.PropTypes.object.isRequired,
        componentProps: React.PropTypes.object.isRequired,
        storeId: React.PropTypes.number.isRequired
    },

    componentWillMount: function () {
        let subComponents = {
            chart: {},
            table: {}
        };

        subComponents.chart.storeId = stateStore.registerComponent(this.props.storeId, {});
        configStore.registerComponent(subComponents.chart.storeId, {});

        subComponents.table.storeId = stateStore.registerComponent(this.props.storeId, {});
        configStore.registerComponent(subComponents.table.storeId, {});

        stateStore.initializeComponentState(this.props.storeId,
            {
                accountType: AccountTypes.EXPENSE,
                displayMode: "chart",
                subComponents: subComponents,
                areaList: null,
                selectedLevel: 1,
                selectedArea: -1
            });
    },

    /*
     * Rendering and interactivity methods (read from the bottom)
     */

    renderCharts: function () {
        let subComponents = stateStore.getValue(this.props.storeId, 'subComponents');
        return (
            <div>
                <ChangesChart site={this.props.site}
                              storeId={subComponents.chart.storeId}
                              datasets={this.props.componentData['mydatasets'].ids}
                              accountType={stateStore.getValue(this.props.storeId, 'accountType')}
                              selectedLevel={stateStore.getValue(this.props.storeId, 'selectedLevel')}
                    />
            </div>
        )
    },

    renderTable: function () {
        let subComponents = stateStore.getValue(this.props.storeId, 'subComponents');

        return (
            <div>
                <ChangesTable site={this.props.site}
                                storeId={subComponents.table.storeId}
                                datasets={this.props.componentData['mydatasets'].ids}
                                accountType={stateStore.getValue(this.props.storeId, 'accountType')}
                                selectedLevel={stateStore.getValue(this.props.storeId, 'selectedLevel')}
                    />
            </div>
        )
    },

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

    render: function () {
        let displayMode = stateStore.getValue(this.props.storeId, 'displayMode');
        let renderFunction = (displayMode == "chart")?this.renderCharts:this.renderTable;
        return (
            <div>
                {this.optionsPanel()}
                {renderFunction()}
            </div>
        )
    }
});

export default WhatsNewPage;

