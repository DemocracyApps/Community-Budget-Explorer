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
        var el = React.findDOMNode(this.refs.myChart);
        $(el).children().remove();
        d3BarChart.create(el, {width: this.props.width, height: this.props.height}, this.props.data);
    },

    componentDidUpdate: function() {
        var el = React.findDOMNode(this.refs.myChart);
        $(el).children().remove();
        d3BarChart.create(el, {width: this.props.width, height: this.props.height}, this.props.data);
    },

    componentWillUnmount: function () {

    },


    render: function() {


        return (
            <div>
                <div className="Chart" ref="myChart"></div>
            </div>
        )

    }
});

export default VerticalBarChart;

