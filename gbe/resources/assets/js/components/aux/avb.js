import d3 from 'd3';

var avb = {};

avb.chart = require('./avb/chart');
avb.cards = require('./avb/cards');
avb.treemap = require('./avb/treemap');
avb.table = require('./avb/table');
avb.navbar = require('./avb/navbar');

var statistics = require('./avb/statistics');


// navigation variables

avb.root = null; // reference to root node of current section
avb.section = null; // current selected section
avb.mode = null; // current mode (map, table etc.)
avb.data = {}; // json data
avb.currentNode = {}; // currently selected node

// time variables

// first datapoint
avb.firstYear = null;
// last datapoint
avb.lastYear = null;
//avb.currentYear = new Date().getFullYear();
avb.currentYear = 2014;
avb.thisYear = avb.currentYear;

// amount of yearly taxes spent by user
avb.userContribution = null;
// available data sections
avb.sections = ['revenues', 'expenses'];
// Arlington version:
//avb.sections = ['revenues', 'expenses', 'funds'];
// available modes (treemap, table..)
avb.modes =
{
    "l" : {
        js : avb.table,
        template : '#table-template',
        container : '#table-container'
    },
    "t" : {
        js : avb.treemap,
        template : '#treemap-template',
        container : '#navigation'
    }
}

var timer = 0;

// Protoypes

/*
 * Converts number to css compatible value
 */
Number.prototype.px = function () {
    return this.toString() + "px";
};

/*
 *   Reads parameters from current url path and calls related
 *   initialization routines
 */
function initialize(incomingData){
    //var urlComponents = window.location.pathname.substring(1).split('/');
    var params = {
        section : "expenses",
        year : "2014",
        mode : "t",
        node : null
    };
    avb.navbar.initialize(incomingData);
    initializeVisualizations(params, incomingData);
}

/*
 *  Initializes data visualization components
 *
 *  @param {obj} params - year, mode, section and node
 */
function initializeVisualizations(params, incomingData) {

    // get previously set year
    var yearCookie = parseInt(jQuery.cookie('year'));
    // use year listed in the params object
    if (params.year !== undefined && !isNaN(parseInt(params.year))) {
        avb.thisYear = params.year;
        // use year previosly set (if any)
    } else if (!isNaN(yearCookie)) {
        avb.thisYear = yearCookie;
    } else {

    }
    avb.section = params.section;

    // highlight current selection in navigation bar
    $('.section').each(function () {
        if ($(this).data('section') === avb.section.toLowerCase()) {
            $(this).addClass('selected');
        }
    });

    avb.userContribution = null;

    // set viewing mode
    setMode(params.mode);

    // connect search actions
    $('#searchbox').keyup(avb.navbar.searchChange);

    loadData(incomingData);
}

/*
 *   Parses JSON files and calls visualization subroutines
 */
function loadData(incomingData) {

    $.each(avb.sections, function (i, url) {
        avb.data[url] = incomingData;
    });

    // initialize root level
    avb.root = avb.data[avb.section];

    // initialize year variables based on data

    // determine oldest year
    avb.firstYear = d3.min(avb.root.values, function (d) {
        return d.year
    });

    // determine newest year
    avb.lastYear = d3.max(avb.root.values, function (d) {
        return d.year
    });
    avb.yearIndex = avb.thisYear - avb.firstYear;
    statistics.computeStats(avb);
    avb.navbar.initialize(avb.thisYear, avb.firstYear, avb.lastYear);

    avb.currentNode.data = undefined;

    // initialize cards
    avb.cards.initialize(statistics.stats.decks, avb.section);


    // Possibly clean up child nodes of container:
    $(avb.modes[avb.mode].container).children("svg").remove();
    // navigation (treemap or table)
    avb.navigation.initialize($(avb.modes[avb.mode].container), avb.root, avb);
    avb.navigation.open(avb.root.hash, null, avb);
}

/*
 *   Mode selection subroutines
 */

/*
 *   Sets visualization mode
 *
 *   @param {string} mode - 'l' for list, 't' for treemap
 */
function setMode(mode) {
    var $container = $('#avb-wrap');
    if (! $container) throw "No damn container";
    mode = mode || "t";
    avb.mode = mode;
    avb.navigation = avb.modes[mode].js;
}

/*
 * Switches between visualization models
 *
 * @param {string} mode - visualization mode ('l' for list, 't' for treemap)
 * @param {bool} pushurl - whether to push change in browser history
 */
function switchMode(mode, pushurl) {
    if (pushurl === undefined) pushurl = true;
    setMode(mode);
    if (pushurl) pushUrl(avb.section, avb.thisYear, mode, avb.root.hash);
    loadData();
}

/*
 *   Year selection subroutines
 */

/*
 * Switches visualizations to selected year
 *
 * @param {int} year - selected year
 *
 */
function changeYear(year) {
    // don't switch if year is already selected
    if (year === avb.thisYear) return;

    // push change to browser history
    pushUrl(avb.section, year, avb.mode, avb.root.hash);
    // set new year values
    avb.thisYear = year;
    avb.yearIndex = avb.thisYear - avb.firstYear;
    // update navigation (treemap or table)
    avb.navigation.update(avb.root);

    avb.navigation.open(avb.currentNode.data.hash, null, avb);
    // remember year over page changes
    $.cookie('year', year, {
        expires: 14
    });
    // update homepage graph if needed
    if ($('#avb-home').is(":visible")) {
        avb.home.showGraph(100);
    }
}

/*
 *   Helper functions
 */

/* As simple as that */
var log = function (d) {
    console.log(d);
}


/*
 *   Applies translate to svg object
 */
function translate(obj, x, y) {
    obj.attr("transform", "translate(" + (x).toString() + "," + (y).toString() + ")");
}

/*
 *  Centers object vertically
 */
$.fn.center = function () {
    this.css("margin-top", Math.max(0, $(this).parent().height() - $(this).outerHeight()) / 2);
    return this;
}

/*
 *   Resizes text to match target width
 *
 *   @param {int} maxFontSize - maxium font size
 *   @param {int} targetWidth - desired width
 */
$.fn.textfill = function (maxFontSize, targetWidth) {
    var fontSize = 10;
    $(this).css({
        'font-size': fontSize
    });
    while (($(this).width() < targetWidth) && (fontSize < maxFontSize)) {
        fontSize += 1;
        $(this).css({
            'font-size': fontSize
        });
    }
    $(this).css({
        'font-size': fontSize - 1
    });

};

/*
 *   Capitalizes a string
 *
 *   @param {string} string - string to be capitalized
 *   @return {string} - capitalized string
 */
function capitalize(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function findSection(hash){
    var section = null;
    $.each(avb.data, function(){
        if(findHash(hash, this) !== false) {
            section = this;
        }
    })
    return section;
}


//        <script type="text/javascript" src ="/js/statistics.js"></script>
//        <script type="text/javascript" src ="/js/home.js"></script>
//        <script type="text/javascript" src ="/js/avb.js"></script>

export default {
    avb: avb,
    initialize: initialize
};
