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
        stateStore.initializeGlobalState('site', {'currentPage': this.props.site.startPage});
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

    /*
     *   Pushes current status to browser history
     *
     *   @param {string} section - current section
     *   @param {int} year - current year
     *   @param {string} mode - treemap or table view
     *   @param {string} node - hash of current node
     *
     */
    pushUrl: function (page) {
        //if (utilities.ie()) return;
        // format URL
        var baseUrl = this.props.site.baseUrl;
        if (!baseUrl.endsWith('/')) baseUrl += '/';
        var url = baseUrl + page;
        var nParams = 0;
        if ('embedded' in this.props.site) nParams++;
        if ('maxWidth' in this.props.site) nParams++;

        if (nParams > 0) {
            var added = "?";
            if (this.props.site.embedded) {
                url += "?embedded=true";
                if ('maxWidth' in this.props.site) url += "&max-width="+this.props.site.maxWidth;
            }
            else if ('maxWidth' in this.props.site) url += "?max-width=" + this.props.site.maxWidth;
        }
        // create history object
        window.history.pushState({
            page: page,
            embedded: this.props.site.embedded
        }, "", url);
    },

    render: function() {
        var currentPage = stateStore.getGlobalValue('site','currentPage');
        var embedded = this.props.site.embedded;
        var page = configStore.getConfiguration('pages', currentPage);
        var layoutProps = {
            site: this.props.site,
            layout: page.layout,
            components: page.components,
            reactComponents:this.props.reactComponents
        };

        this.pushUrl(page.shortName);
        var pageDescription = function (page) {
            if (page.description != null) {
                return <div><p>{page.description}</p><hr/></div>
            }
        };
        //if we want fixed top, add class 'navbar-fixed-top' to the 'nav' element

        if (embedded) {
            return (
                <div className="container">
                    <div className="container-fluid">
                        <div className="row">
                            <div className="col-md-12">
                                <h1>{this.pageTitle(page)}</h1>
                                {pageDescription(page)}
                            </div>
                        </div>
                    </div>
                    <div className="container-fluid site-body">
                        <BootstrapLayout {...layoutProps}/>
                    </div>
                </div>
            );
        }
        else {
            return (
                <div className="container">

                    <SiteNavigation site={this.props.site} pages={this.props.pages}/>

                    <div className="container-fluid">
                        <div className="row">
                            <div className="col-md-12">
                                <h1>{this.pageTitle(page)}</h1>
                                {pageDescription(page)}
                            </div>
                        </div>
                    </div>
                    <div className="container-fluid site-body">
                        <BootstrapLayout {...layoutProps}/>
                    </div>
                    <div style={{minHeight:50}}></div>
                    <div className="container-fluid site-footer">
                        <div className="row">
                            <div className="col-xs-12">
                                <span style={{float: "right"}}>Powered by <a href="http://democracyapps.us"
                                                                             target="_blank">DemocracyApps</a></span>
                            </div>
                        </div>
                    </div>
                </div>
            );
        }
    }
});

export default Site;
