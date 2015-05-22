import React from 'react';
import d3 from 'd3';

var d3BarChart = require('./aux/D3BarChart');


var VerticalBarChart = React.createClass({
    propTypes: {
        data: React.PropTypes.array.isRequired,
        width: React.PropTypes.number.isRequired,
        height: React.PropTypes.number.isRequired
    },



    componentDidMount: function() {
        var el = React.findDOMNode(this);
        console.log("I have width = " + this.props.width);
        var it = d3BarChart.create(el, {width: this.props.width, height: this.props.height}, this.props.data);
        window.it = it;
    },

    componentDidUpdate: function() {

    },

    componentWillUnmount: function () {

    },

    render: function() {


        return (
            <div className="Chart"></div>
        )

    }
});

export default VerticalBarChart;

