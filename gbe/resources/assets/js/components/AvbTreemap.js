import React from 'react';
import d3 from 'd3';

var d3BarChart = require('./aux/D3BarChart');
var avbStuff = require('./aux/avb.js');

var AvbTreemap = React.createClass({
    propTypes: {
        data: React.PropTypes.array.isRequired,
        width: React.PropTypes.number.isRequired,
        height: React.PropTypes.number.isRequired
    },

    getInitialState: function() {
        return {
            hoverMessage: "Mouse over bars to see details"
        };
    },

    componentDidMount: function() {
        var el = React.findDOMNode(this.refs.myChart);
        avbStuff.initialize();
    },

    componentDidUpdate: function() {

    },

    componentWillUnmount: function () {

    },

    render: function() {


        return (
        <div className="container" id="avb-body" style={{width:1200, height:600}}>
            <div className="row-fluid span12" id="avb-wrap">
                <div id="information-container" className="span6" style={{position:"relative", paddingLeft:5}}>

                    <div id="information-cards" >

                        {{ /* entry title */ }}
                        <div className="title-head" style={{height:70}}>
                            <div style={{display:"inline-block"}} className="text" > </div>
                        </div>

                        <div id="info-wrap" >
                            <div id="slider-wrap">

                                {{ /* layer chart legend */ }}
                                <div id="legend-wrap">
                                    <div className="arrow" style={{right:20}}>
                                        <i className="icon-chevron-left"></i>
                                    </div>
                                    <div id="legend-container">
                                        <div id="legend" className="separator">
                                            <table><tbody></tbody></table>
                                        </div>
                                    </div>
                                </div>

                                {{ /*  info cards */}}
                                <div id="cards" >
                                    <div className="arrow">
                                        <i className="icon-chevron-right"></i>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    {{ /*  chart */ }}
                    <div id="chart-wrap" className="row-fluid" >
                        <div id='chart' className="chart"> </div>
                    </div>
                </div>

                {{ /*  treemap */ }}
                <div id="navigation-container" className="span6" >
                    <div className="title-head" style={{height:70}}>
                        <button id="zoombutton" className="btn pull-right">
                            <i className="icon-zoom-out"></i> Go back
                        </button>
                    </div>
                    <div id="navigation" className="row-fluid">
                        <div id="ie-popover">
                            <div className="text"></div>
                            <div className="arrow"> </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        )

    }
});

export default AvbTreemap;

