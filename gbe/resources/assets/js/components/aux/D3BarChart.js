import d3 from 'd3';

var d3Chart = {};

d3Chart.create = function (el, props, data, callbacks) {
    var margin = {top: 20, right: 35, bottom: 10, left: 25};
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

d3Chart.rescale = function(value) {
    // round up to nearest 1000
    var tmp = Math.trunc(Math.abs(value)/1000);
    tmp = (tmp+1)*1000;
    return (value<0.0)?-tmp:tmp;
};

d3Chart.computeExtent = function(data) {
    var extent = d3.extent(data, function (d) { return d.value; });
    if (extent[0] > 0) extent[0] = 0;
    if (Math.abs(extent[0]) > extent[1]) extent[1] = Math.abs(extent[0]);
    extent[0] = this.rescale(extent[0]);
    extent[1] = this.rescale(extent[1]);
    return extent;
};

d3Chart.drawBars = function(el, scales, data, height) {
    var extent = this.computeExtent(data);
    var minValue = extent[0];
    var maxValue = extent[1];
    if (minValue > 0.) minValue = 0.;

    var tooltip = d3.select('body').append('div').attr('class', 'bartooltip');

    tooltip.append('div')
        .attr('class', 'barlabel');
    tooltip.select('.label').html("<p>This is the default text in case it matters.</p>");

    var svg = d3.select(el).selectAll(".d3");

    var mouseOver = function(d) {
        tooltip.select('.barlabel').html("<p>"+d.name+"</p>");
        tooltip.style('top', (d3.event.pageY + 30) + 'px')
            .style('left', (d3.event.pageX + 20) + 'px');
        tooltip.style('display', 'block');
    };
    var mouseOut = function(d) {
        tooltip.style('display', 'none');
    };

    svg.selectAll(".bar")
        .data(data)
        .enter().append('rect')
        .attr("class", function(d) { return (d.value < 0)?"bar negative":"bar positive"})
        .attr("x", function(d) {
            var x = scales.x(Math.min(0,d.value));
            return x;
        })
        .attr("y", function(d) {
            var yval = d.categories.join('/');
            var y= scales.y(yval);
            return y;
        })
        .attr("width", function(d) {
            var w = Math.abs(scales.x(d.value) - scales.x(0));
            if (!d.show) w=0;
            return w;
        })
        .attr("height", scales.y.rangeBand())
        .on('mouseover', mouseOver)
        .on('mouseout', mouseOut);
    var txtX = .0075 * maxValue;
    svg.selectAll(".bartext")
        .data(data)
        .enter().append('text')
        .attr("class", function(d) { return (d.value < 0)?"bartext negative":"bartext positive"})
        .text(function (d) { if (!d.show) return ""; return d.name + "(" + d.percent + ")"; })
        .attr("x", scales.x(txtX))
        .attr("y", function(d) {
            var yval = d.categories.join('/');
                var y = scales.y(yval) + 1.5 * scales.y.rangeBand();
                return y;
        });

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
    var extent = this.computeExtent(data);
    if (extent[0] > 0) extent[0] = 0;
    if (Math.abs(extent[0]) > extent[1]) extent[1] = Math.abs(extent[0]);
    var x = d3.scale.linear()
        .domain(extent)
        .range([margin.left,width-(margin.right+margin.left)])
        .nice();

    var y = d3.scale.ordinal()
        .domain(data.map(function(d) {return d.categories.join('/');}))
        .rangeRoundBands([margin.bottom,height-(margin.top+margin.bottom)], .5, .3);
    return {x: x, y: y};
};

export default d3Chart;
