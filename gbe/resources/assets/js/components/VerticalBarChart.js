import React from 'react';
import d3 from 'd3';

var d3BarChart = require('./aux/D3BarChart');


var VerticalBarChart = React.createClass({
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
        var callbacks = {
            id: 12,
            mouseOver: function(d) {
                this.setState({hoverMessage: JSON.stringify(d)});
            }.bind(this),
            mouseOut: function(d) {
                this.setState({hoverMessage: "Mouse over bars to see details"});
            }.bind(this)
        };
        var it = d3BarChart.create(el, {width: this.props.width, height: this.props.height}, this.props.data, callbacks);
        window.it = it;
    },

    componentDidUpdate: function() {

    },

    componentWillUnmount: function () {

    },

    hoverMessage: function() {
        if (this.state.hoverMessage != null) {
            return <p>{this.state.hoverMessage}</p>
        }
    },

    render: function() {


        return (
            <div>
                <div className="Chart" ref="myChart"></div>
                <div>{this.hoverMessage()}</div>
            </div>
        )

    }
});

export default VerticalBarChart;

