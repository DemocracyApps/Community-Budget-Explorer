import React from 'react';
import d3 from 'd3';

var d3BarChart = require('./aux/D3BarChart');
var avbStuff = require('./aux/avb.js');

import md5 from 'blueimp-md5';
var AvbTreemap = React.createClass({
    propTypes: {
        data: React.PropTypes.object.isRequired,
        width: React.PropTypes.number.isRequired,
        height: React.PropTypes.number.isRequired,
        accountType: React.PropTypes.string.isRequired
    },

    findNext: function(current, key) {
        let index = -1;
        for (let i=0; index < 0 && i<current.sub.length; ++i) {
            if (key == current.sub[i].key) {
                index = i;
            }
        }
        return index;
    },

    prepareData: function(dataset) {
        let years = dataset.dataHeaders;
        let inData = dataset.data;
        let nPeriods = inData[0].amount.length;

        var createValues = function (inValues) {
            let values = new Array(nPeriods);
            for (let i=0; i<nPeriods; ++i) {
                values[i] = {
                    val: (inValues != null)?inValues[i]:0.0,
                    year: years[i]
                };
            }
            return values;
        };

        var tree = {
            key: this.props.accountType,
            hash: md5.md5(this.props.accountType),
            src: "",
            descr: "",
            url:"",
            values: createValues(null),
            sub: []
        }

        // NOTE: This assumes that we won't hit the same full path twice. If we do, the bottom will be wrong.
        for (let i=0; i<inData.length; ++i) {
            let item = inData[i];
            //let hash = md5.md5(item.categories.join());
            let current = tree;
            let nLevels = item.categories.length;
            let path = "";
            for (let i = 0; i < nLevels; ++i) {
                let key = item.categories[i];
                path += key;
                let nextIndex = this.findNext(current, key);
                if (nextIndex< 0) {
                    let vals = (i==nLevels-1)?createValues(item.amount):createValues(null);
                    let node = {
                        key: key,
                        hash: md5.md5(path),
                        src: "",
                        descr: "",
                        url:"",
                        values: vals,
                        sub: []
                    };
                    nextIndex = current.sub.length;
                    current.sub.push(node);
                }
                if (i < nLevels) {
                    for (let j=0; j<item.amount.length; ++j) {
                        current.values[j].val += item.amount[j];
                    }
                    current = current.sub[nextIndex];
                }
            }
        }
        return tree;
    },

    componentDidMount: function() {
        var el = React.findDOMNode(this.refs.myChart);
        if (this.props.data != null) {
            var data = this.prepareData(this.props.data);
        }
        avbStuff.initialize(data, this.props.year);
    },

    componentDidUpdate: function() {
        if (this.props.data != null) {
            var data = this.prepareData(this.props.data);
        }
        avbStuff.initialize(data, this.props.year);
    },

    render: function() {


        return (
        <div className="container" id="avb-body" style={{width:this.props.width, height:this.props.height}}>
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

