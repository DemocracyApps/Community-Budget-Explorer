import React from 'react';
import BootstrapLayout from './BootstrapLayout';

import SiteNavigation from './SiteNavigation';
var dispatcher = require('../common/BudgetAppDispatcher');
var ActionTypes = require('../constants/ActionTypes');

var configStore = require('../stores/ConfigStore');
var stateStore = require('../stores/StateStore');
var datasetStore = require('../stores/DatasetStore');

var Site = React.createClass({

    propTypes: {
        site: React.PropTypes.object.isRequired,
        pages: React.PropTypes.array.isRequired,
        configurationId: React.PropTypes.oneOfType([
            React.PropTypes.string,
            React.PropTypes.number
        ]).isRequired
    },

    getInitialState: function() {
        return {
            myStateId: null,
            version: 0
        };
    },

    componentWillMount: function() {
        stateStore.registerState('site.currentPage', this.props.site.startPage);
    },

    componentDidMount: function () {
        stateStore.addChangeListener(this._onStateChange);
        datasetStore.addChangeListener(this._onDataChange);
    },

    _onDataChange: function () {
        this.setState({version: this.state.version++});
    },

    _onStateChange: function() {
        // Don't really care what the change was, we'll re-render
        // Any component that needs to be efficient can do it via componentWillUpdate
        this.setState({version: this.state.version++});
    },

    pageTitle: function(page) {
        if (page.title != undefined) {
            return <span>{page.title}</span>
        }
    },

    m: function() {
        var res = {};
        for (var i=0; i<arguments.length; ++i) {
            if (arguments[i]) {
                Object.assign(res, arguments[i]);
            }
        }
        return res;
    },

    render: function() {

        var currentPage = stateStore.getValue('site.currentPage');
        var page = configStore.getConfiguration('pages', currentPage);

        var layoutProps = {
            layout: page.layout,
            components: page.components,
            reactComponents:this.props.reactComponents
        };

        var pageDescription = function (page) {
            if (page.description != null) {
                return <div><p>{page.description}</p><hr/></div>
            }
        };
        //if we want fixed top, add class 'navbar-fixed-top' to the 'nav' element

        return (
            <div className="container">

                <SiteNavigation site={this.props.site} pages={this.props.pages}/>

                <div className="container-fluid">
                    <div className="row">
                        <div className="col-xs-12">
                            <h3 style={{marginTop:5}}>{this.pageTitle(page)}</h3>
                            {pageDescription(page)}
                        </div>
                    </div>
                </div>
                <div className="container-fluid site-body">
                    <BootstrapLayout {...layoutProps}/>
                </div>
                <div style={{minHeight:70}}></div>
                <div className="container-fluid site-footer">
                    <div className="row">
                        <div className="col-xs-12" >
                            <span style={{float: "right"}}>Powered by <a href="http://democracyapps.us" target="_blank">DemocracyApps</a></span>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
});

export default Site;
