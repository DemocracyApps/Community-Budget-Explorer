import d3 from 'd3';

var d3Chart = {};

d3Chart.create = function (el, props, data, callbacks) {
    var margin = {top: 20, right: 10, bottom: 10, left: 10};
    var svg = d3.select(el).append('svg')
        .attr('class', 'd3')
        .attr('width', props.width)
        .attr('height', props.height)
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")")
        ;
    this.update(el, data, props.width, props.height, margin, callbacks);
    return svg;
};

d3Chart.update = function(el, data, width, height, margin, callbacks) {
    var scales = d3Chart.computeScales(data, width, height, margin);

    this.drawBars(el, scales, data, height, callbacks);

};

d3Chart.drawBars = function(el, scales, data, height, callbacks) {

    var minValue = d3.min(data, function(d) { return d.value;});
    var maxValue = d3.max(data, function(d) { return d.value;});
    var svg = d3.select(el).selectAll(".d3");
    svg.selectAll(".bar")
        .data(data)
        .enter().append('rect')
        .attr("class", function(d) { return (d.value < 0)?"bar negative":"bar positive"})
        .attr("x", function(d) {
            var x = scales.x(Math.min(0,d.value));
            return x;
        })
        .attr("y", function(d) {
            var y= scales.y(d.name);
            return y;
        })
        .attr("width", function(d) {
            var w = Math.abs(scales.x(d.value) - scales.x(0));
            return w;
        })
        .text(function (d) { return d.name; })
        .attr("height", scales.y.rangeBand())
        .on('mouseover', callbacks.mouseOver)
        .on('mouseout', callbacks.mouseOut);

    var xAxis = d3.svg.axis()
        .scale(scales.x)
        .orient("top")
        .tickValues([minValue, maxValue])
        .tickFormat(d3.format("<-$120,.0f"));

    svg.append("g")
        .attr("class", "x axis")
        .call(xAxis);
    svg.append("g")
        .attr("class", "y axis")
       .append("line")
        .attr("x1", scales.x(0))
        .attr("x2", scales.x(0))
        .attr("y2", height);
};

d3Chart.computeScales = function(data, width, height, margin) {
    var x = d3.scale.linear()
        .domain(d3.extent(data, function (d) { return d.value; }))
        .range([margin.left,width-(margin.right+margin.left)])
        .nice();

    var y = d3.scale.ordinal()
        .domain(data.map(function(d) {return d.name;}))
        .rangeRoundBands([margin.bottom,height-(margin.top+margin.bottom)], .5, .3);
    return {x: x, y: y};
};

export default d3Chart;
