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
        this.setState({version: this.state.version++});
    },

    render: function() {

        var currentPage = stateStore.getStateValue('site.currentPage');

        var page = configStore.getConfiguration('pages', currentPage);

        var layoutProps = {
            layout: page.layout,
            components: page.components,
            reactComponents:this.props.reactComponents
        };

        return (
            <div>
                <div className="container gbe-header">
                    <div className="row">
                        <div className="col-md-6 hdr-left">
                            <h1>{this.props.site.name}</h1>
                        </div>
                        <div className="col-md-6 hdr-right">
                            <SiteNavigation site={this.props.site} pages={this.props.pages}/>
                        </div>
                    </div>
                </div>

                <div className="container gbe-body">
                    <BootstrapLayout {...layoutProps}/>
                </div>
            </div>
        );
    }
});

export default Site;
