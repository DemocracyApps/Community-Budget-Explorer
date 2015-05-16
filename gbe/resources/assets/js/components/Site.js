import React from 'react';
import BootstrapLayout from './BootstrapLayout';

import SiteNavigation from './SiteNavigation';

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
                return <h2>{page.title}</h2>
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

        var siteHeaderStyles = {
            headerStyle: {
                minHeight: 50
            },
            brandTitleStyle: {
                fontWeight: "lighter",
                fontSize: "1.75em"
            },
            siteNavigationStyles: {
                navProps: {
                    fontSize: "1em",
                    fontWeight: "600",
                    padding: "10px 0px 0px 0px",
                    float: "right"
                }
            },
            hrProps: {
                padding: "0px 0px 0px 0px",
                margin: "0px 0px 0px 0px",
                border: "0px 0px 0px 0px"
            }

        };

        return (
            <div>
                <div className="container site-header" style={this.m(siteHeaderStyles.headerStyle)}>
                    <div className="row">
                        <div className="col-xs-6 site-hdr-brand">
                            <div className="site-brand" style={siteHeaderStyles.brandStyle}>
                                <h1 style={siteHeaderStyles.brandTitleStyle}>
                                    <a href={this.props.site.baseUrl}>{this.props.site.name}</a>
                                </h1>
                            </div>
                        </div>
                        <div className="col-xs-6 navigation site-navbar">
                            <SiteNavigation site={this.props.site} pages={this.props.pages} styleProps={siteHeaderStyles.siteNavigationStyles}/>
                        </div>
                        <div className="col-xs-12" style={{padding: "0px", margin: "0px", border:"0px"}}>
                            <hr style={siteHeaderStyles.hrProps}/>
                        </div>
                    </div>
                </div>

                <div className="container site-body">
                    {this.pageTitle(page)}
                    <BootstrapLayout {...layoutProps}/>
                </div>

                <div className="container site-footer">
                    <div className="row">
                        <hr/>
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
