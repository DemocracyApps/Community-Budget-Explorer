/*
File: treemap.js

Description:
    Treemap component for visual budget application.

Authors:
    Ivan DiLernia <ivan@goinvo.com>
    Roger Zhu <roger@goinvo.com>

 Modified by Eric Jackson <eric.jackson@democracyapps.us>

License:
    Copyright 2013, Involution Studios <http://goinvo.com>

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

      http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
*/

var utilities = require('./utilities');

var avb_treemap = function () {
    var urlPushAllowed = false;
    var localAvb = null;
    var nav, currentLevel,
        // holds rgb values for white
        white = {
            r: 255,
            b: 255,
            g: 255
        };

    var ie = null;

    var importFunction = function (ie_in) {
        ie = ie_in;
    }

    /*
    *   Initialize navigation treemap
    *
    *   @param {jquery selection} $container - treemap container
    *   @param {node} data - root node that will become root level of treemap
    */
    var initialize = function ($container, data, avb) {
        localAvb = avb;
        var width = $container.width(),
            height = $container.height();
        var height = height,
            formatNumber = d3.format(",d"),
            transitioning;

        // create svg
        var tmp = d3.select($container.get(0));

        nav = d3.select($container.get(0)).append("svg")
            .attr("width", width)
            .attr("height", height)
            .append("g")
            .style("shape-rendering", "crispEdges");

        // initialize x and y scales
        nav.x = d3.scale.linear()
            .domain([0, width])
            .range([0, width]);

        nav.y = d3.scale.linear()
            .domain([0, height])
            .range([0, height]);

        nav.h = height;
        nav.w = width;

        // color scale
        nav.color = d3.scale.category20();

        // center zoom button vertically
        $('#zoombutton').center();

        // initialize chart
        avb.chart.initialize($('#chart'), avb);

        avb.currentNode.data = data;

        // start populating treemap
        update(data, avb);

    };

        /*
        *   Computes treemap layout for a nested dataset.
        *   Treemap data will be stored within each node object.
        *
        *   @param {node} data - node where treemap should begin
        */
    var update = function (data, avb) {
        localAvb = avb;
            // remove all old treemap elements
            nav.selectAll("g").remove();

            // this function recursively determines
            // treemap layout structure
            var layout = function (d) {
                // root color
                d.color = nav.color(0);

                if (d.sub) {
                    treemap.nodes({
                        values: d.values,
                        children: d.sub
                    });
                    d.sub.forEach(function (c,i) {
                        c.x = d.x + c.x * d.dx;
                        c.y = d.y + c.y * d.dy;
                        c.dx *= d.dx;
                        c.dy *= d.dy;
                        c.parent = d;
                        layout(c);
                        // node color
                        c.color = nav.color(i);
                    });
                }
            }

            // initialize treemap
            var init = function (root) {
                root.x = root.y = 0;
                root.dx = nav.w;
                root.dy = nav.h;
                root.depth = 0;
            }

            // create treemap d3 layout
            var treemap = d3.layout.treemap()
            // node children
            .children(function (d, depth) {
                return depth ? null : d.children;
            })
            // treemap values calculated based on current year value
            .value(function (d) {
                return d.values[avb.yearIndex].val
            })
            // block sorting function
            .sort(function (a, b) {
                return a.values[avb.yearIndex].val - b.values[avb.yearIndex].val;
            })
                .ratio(nav.h / nav.w * 0.5 * (1 + Math.sqrt(5)))
                .round(false);

            var root = data;

            nav.grandparent = nav.append("g")
                .attr("class", "grandparent");

            // init treemap 
            init(root);
            // init layout
            layout(root);

            // display treemap
            currentLevel = display(avb.currentNode.data, avb);
        };

        /*
        *   Draws and displays a treemap layout from node data
        *
        *   @param {node} d - node where treemap begins (root)
        */
        var display = function (d, avb) {

            // remove all popovers
            $('.no-value').popover('destroy');

            var formatNumber = d3.format(",d"),
                // flag will be used to avoid overlapping transitions
                transitioning;

            // return block name
            function name(d) {
                return d.parent ? name(d.parent) + "." + d.key : d.key;
            }

            // insert top-level blocks
            var g1 = nav.insert("g", ".grandparent")
                .datum(d)
                .attr("class", "depth")
                .on("click", function (event) {
                    zoneClick.call(this, d3.select(this).datum(), true, 1, avb);
                })

            // add in data
            var g = g1.selectAll("g")
                .data((d.sub.length === 0) ? [d] : d.sub)
                .enter().append("g");

            // create grandparent bar at top 
            nav.grandparent
                .datum((d.parent === undefined) ? d : d.parent)
                .attr("nodeid", (d.parent === undefined) ? d.hash : d.parent.hash)
                .on("click", function (event) {
                    zoneClick.call(this, d3.select(this).datum(), true, 1, avb);
                });

            // refresh title
            updateTitle(d, avb);

            /* transition on child click */
            g.filter(function (d) {
                return d.sub;
            })
                .classed("children", true)
                // expand when clicked
                .on("click", function (event) {
                    zoneClick.call(this, d3.select(this).datum(), true, 1, avb);
                })
                .each(function () {
                    var node = d3.select(this);
                    // assign node hash attribute
                    node.attr('nodeid', function () {
                        return node.datum().hash;
                    });
                });

            // draw parent rectange
            g.append("rect")
                .attr("class", "parent")
                .call(rect)
                .style("fill", function (d) {
                   return utilities.applyTransparency(d.color, 0.8);
                });

            var maxDrawLevel = 1;
            // recursively draw children rectangles
            function addChilds(d, g, level) {
                // add child rectangles
                ++level
                g.selectAll(".child")
                    .data(function (d) {
                        return d.sub || [d];
                    })
                    .enter().append("g")
                    .attr("class", "child")

                // propagate recursively to next depth
                .each(function () {
                    var group = d3.select(this);
                    if (level < maxDrawLevel && d.sub !== undefined) {
                        $.each(d.sub, function () {
                            addChilds(this, group, level);
                        })
                    }
                })
                    .append("rect")
                    .call(rect);
            }

            addChilds(d, g, 0);

            // IE popover action
            if (utilities.ie()) {
                nav.on('mouseout', function () {
                    d3.select('#ie-popover').style('display', 'none')
                });
                return g;
            }
            // assign label through foreign object
            // foreignobjects allows the use of divs and 
            // textwrapping
            g.each(function () {
                var label = d3.select(this).append("foreignObject")
                    .call(rect)
                    .attr("class", "foreignobj")
                    .append("xhtml:div")
                    .html(function (d) {
                        var title = '<div class="titleLabel">' + d.key + '</div>',
                            values = '<div class="valueLabel">' + utilities.formatcurrency(d.values[avb.yearIndex].val) + '</div>';
                        return title + values;
                    })
                    .attr("class", "textdiv");
                textLabels.call(this, d, avb);

            });

            return g;

        };

    /*
    *   Assigns label and popover events (IE only)
    *   IE9 does not support SVG foreign objects
    *
    *   @param {node} d - node object to which popover has to be attached
    */
    var ieLabels = function (d,avb) {

        /*
        * Attach popover event to zone
        */
        function attachPopoverIe(obj, title, descr) {
            d3.select(obj).on('mouseover', function () {
                var rect = d3.select(this).select('.parent');
                var coords = [parseFloat(rect.attr('x')),
                    parseFloat(rect.attr('y'))
                ];
                var x = coords[0] + parseFloat(rect.attr('width')) / 2 - 75;
                d3.select('#ie-popover').select('.text').text(title);
                d3.select('#ie-popover').style('display', 'block')
                    .style('left', (x).px()).style('top', (coords[1]).px());
            })
        }

        // label zone using svg:text object
        var label = d3.select(this).append("text")
            .call(rect).attr('dy', '1.5em').attr('dx', '0.5em')
            // assign label name
            .text(function (d) {
                return d.key
            })
        textLabels.call(this,d, avb);

        var d = d3.select(this).datum(),
            containerHeight = nav.y(d.y + d.dy) - nav.y(d.y),
            containerWidth = nav.x(d.x + d.dx) - nav.x(d.x);

        // do not show label if zone is too small
        if (containerHeight < 40 || containerWidth < 150) {
            d3.select(this).classed("no-label", true);
            popover = true;
        }

        // attach popover to zone
        attachPopoverIe(this, d.key, d.descr);
    };

    /*
    *   Assigns label and popover events (Chrome, Safari, FF)
    *
    *   @param {node} d - node to be labelled
    */
    var textLabels = function (d, avb) {
        /*
        * Attach popover event to zone
        * Requires bootstrap popovers
        */
        function attachPopover(obj, title, descr) {
            $(obj).find('div').first().popover({
                container: 'body',
                trigger: 'hover',
                placement: function (context, source) {

                    // calculate best position for popover placement
                    // offset is used instead of position due to firefox + svg bug
                    var position = $(source).offset();
                    if (position.top < 200) {
                        return "left";
                    } else {
                        return "top";
                    }
                },
                title: (descr !== '' && d.title !== '') ? d.key : '',
                content: (descr !== '') ? descr : d.key,
                html: true
            });
        }

        var d = d3.select(this).datum(),
            containerHeight = nav.y(d.y + d.dy) - nav.y(d.y),
            containerWidth = nav.x(d.x + d.dx) - nav.x(d.x),
            title = $(this).find('.titleLabel').first(),
            div = $(this).find('.textdiv').first();

        // eliminate old popover and reset zone classes
        $(this).find('div').first().popover('destroy');
        // no-label -> zone is too small to show any text at all
        d3.select(this).classed("no-label", false);
        // no-label -> zobe is too small to show amount label
        d3.select(this).classed("no-value", false);
        // compensates padding
        var labelPadding = 16;
        div.height(Math.max(0, containerHeight - labelPadding));

        // every entry has no popover by default
        var popover = false;

        // Note.
        // If we are in the expenses section and the user did enter his/her
        // tax contribution, popovers will be used to show how much each 
        // zone amounts in terms of personal contribution.
        var description;
        if (avb.userContribution != null && avb.section == 'expenses') {
            // popover content is split in separate 2 divs
            description = '<div>' + d.descr + '</div> <div class="contribution"> Your contribution is ' + stats.individual.value(d) + '</div>';
        } else {
            description = d.descr;
        }

        // calculate whether zone has enough space for any labels
        if (containerHeight < title.outerHeight() || containerHeight < 40 || containerWidth < 60) {
            d3.select(this).classed("no-label", true);
            popover = true;
        }
        // calculate whether zone has enough space for amount label
        if (containerHeight < div.height() || containerHeight < 80 || containerWidth < 90) {
            d3.select(this).classed("no-value", true);
        }
        // attach popover to zone
        if (popover || description !== '' || containerWidth < 80) {
            attachPopover(this, d.key, description);
        }

    };

    /*
    *   Updates page title when sections
    *
    *   @param {node} data - current node
    */
    var updateTitle = function (data, avb) {
        var $title = $(".title-head .text");
        var $zoom = $('#zoombutton');
        var parent = d3.select('.grandparent').node();

        // remove previous action set on zoom button
        $zoom.unbind();
        // set title text
        $title.text(data.key);
        // make sure to shrink text if it does not fit
        // 48px is max text size
        $title.textfill(48, $('.title-head').width() - 120);

        // main section such as revenues, expenses and funds need to have
        // descriptions
        if ($.inArray(data.key.toLowerCase(), avb.sections) > -1) {
            $('<div class="description">  </div>').appendTo($title).html(data.descr);
        }

        // make zoom-out button appear disabled while at root nodes
        if (avb.currentNode.data === avb.root) {
            $zoom.addClass('disabled');
        } else {
            $zoom.removeClass('disabled');
        }

        // zoom button renders parent zone
        if(data !== avb.root) {
            $zoom.click(function () {
                zoneClick.call(parent, d3.select(parent).datum(), true, 1, avb);
            })
        }

    };


    /*
    *   Displays node in treemap
    *
    *   @param {string} nodeId - hash that refers to zone
    *   @param {integer} transition - duration of transition from current node to destination node (optional)
    */
    var open = function (nodeId, transition, avb) {
        localAvb = avb;
        // find node with given hash or open root node
        zoneClick.call(null, utilities.findHash(nodeId, avb.root) || avb.root, false, transition || 1, avb);
    };

    /*
     *   Browser history routines
     *   (Chrome, Safari, FF)
     */

    /*
     *   Back button action
     */
    window.onpopstate = popUrl;

    /*
     *   Pushes current status to browser history
     *
     *   @param {string} section - current section
     *   @param {int} year - current year
     *   @param {string} mode - treemap or table view
     *   @param {string} node - hash of current node
     *
     */
    function pushUrl(section, year, mode, node) {
        if (! urlPushAllowed) return;
        if (utilities.ie()) return;
        // format URL
        var url = '/' + section + '/' + year + '/' + mode + '/' + node;
        // create history object
        window.history.pushState({
            section: section,
            year: localAvb.thisYear,
            mode: mode,
            nodeId: node
        }, "", url);
    }

    /*
     *   Restores previous history state
     *
     *   @param {state obj} event - object containing previous state
     */
    function popUrl(event) {
        if (utilities.ie()) return;

        if (event.state === null) {
            if (localAvb) {
                localAvb.navigation.open(localAvb.root.hash, 500, localAvb);
            }
        } else if (event.state.mode !== avb.mode) {
            switchMode(event.state.mode, false);
        } else {
            avb.navigation.open(event.state.nodeId, 500, localAvb);
        }
    }

    /*
    *   Event triggered on click event in treemap areas
    *
    *   @param {node} d - clicked node data
    *   @param {boolean} click - whether click was triggered
    *   @param {integer} transition - transition duration
    */
    var zoneClick = function (d, click, transition, avb) {
        // stop event propagation
        var event = window.event || event
        utilities.stopPropagation( event );

        transition = transition || 750;

        // do not expand if another transition is happening
        // or data not defined
        if (nav.transitioning || !d) return;

        // go back if click happened on the same zone
        if (click && d === avb.currentNode.data) {
            $('#zoombutton').trigger('click');
            return;
        }

        // push url to browser history
        if (click) {
            pushUrl(avb.section, avb.thisYear, avb.mode, d.hash);
        }

        // reset year
        avb.yearIndex = avb.thisYear - avb.firstYear;

        // 
        if(d.values[avb.yearIndex].val === 0) {
            zoneClick.call(null, d.parent || avb.root.hash, false, 1, avb);
            return;
        }

        // remove old labels
        nav.selectAll('text').remove();

        // remember currently selected section and year
        avb.currentNode.data = d;
        avb.currentNode.year = avb.yearIndex;


        // update chart and cards
        avb.chart.open(d, d.color, avb);
        avb.cards.open(d, avb);

        // prevent further events from happening while transitioning
        nav.transitioning = true;

        // initialize transitions
        var g2 = display(d, avb);
        var t1 = currentLevel.transition().duration(transition);
        var t2 = g2.transition().duration(transition);

        // Update the domain only after entering new elements.
        nav.x.domain([d.x, d.x + d.dx]);
        nav.y.domain([d.y, d.y + d.dy]);

        // Enable anti-aliasing during the transition.
        nav.style("shape-rendering", null);

        // Draw child nodes on top of parent nodes.
        nav.selectAll(".depth").sort(function (a, b) {
            return a.depth - b.depth;
        });

        // Fade-in entering text.
        g2.selectAll(".foreignobj").style("fill-opacity", 0);

        // Transition to the new view
        t1.style('opacity', 0);
        t1.selectAll(".foreignobj").call(rect);
        t2.selectAll(".foreignobj").call(rect);
        t1.selectAll("rect").call(rect);
        t2.selectAll("rect").call(rect);

        // add labels to new elements
        t2.each(function () {
            if (utilities.ie()) return;
            textLabels.call(this,d, avb);
        });
        t2.each("end", function () {
            if (utilities.ie()) {
                ieLabels.call(this,avb);
            } else {
                textLabels.call(this,d, avb);
            }
        });

        // Remove the old node when the transition is finished.
        t1.remove().each("end", function () {
            nav.style("shape-rendering", "crispEdges");
            nav.transitioning = false;

        });
        // update current level
        currentLevel = g2;
    };

    /*
    *   Sets SVG rectangle properties based on treemap node values
    *
    *   @param {d3 selection} rect - SVG rectangle
    */
    var rect = function (rect) {
        rect.attr("x", function (d) {
            return nav.x(d.x);
        })
        .attr("y", function (d) {
            return nav.y(d.y);
        })
        .attr("width", function (d) {
            return nav.x(d.x + d.dx) - nav.x(d.x);
        })
        .attr("height", function (d) {
            return nav.y(d.y + d.dy) - nav.y(d.y);
        });
    };


    return {
        initialize: initialize,
        update: update,
        open: open,
        updateTitle: updateTitle
    }
}();

export default avb_treemap;
