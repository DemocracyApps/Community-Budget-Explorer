
/*
 *   Detects IE browsers
 *
 *   @return - true when browser is IE
 */
function ie(){
    var agent = navigator.userAgent;
    var reg = /MSIE\s?(\d+)(?:\.(\d+))?/i;
    var matches = agent.match(reg);
    if (matches != null) {
        return true
    }
    return false;
}


/*
 *   Finds node with given hash
 *
 *   @param {string} hash - hash to be searched
 *   @param {node} node - current node
 *   @return {node} - node with given hash
 */
function findHash(hash, node){
    var index = node.hash.indexOf(hash);
    // results
    if (index !== -1) return node;
    // propagate recursively
    if(node.sub !== undefined) {
        // propagate to all children
        for(var i=0; i<node.sub.length; i++) {
            var subResults = findHash(hash, node.sub[i]);
            if (subResults) return subResults;
        }
    }
    return false;
}


/*
 *   Stops event propagation (on all browsers)
 *
 *   @param {event object} event - event for which propagation has to be stopped
 */
function stopPropagation(event){
    if(event) {
        event.cancelBubble = true;
        if(event.stopPropagation) event.stopPropagation();
    }
}

/*
 *   Mixes two rgb colors
 *
 *   @param {object} rgb1 - rgb color object
 *   @param {object} rgb2 - rgb color object
 *   @param {float} p - weight (0 to 1)
 *   @return {rgb object} - mixed color
 */
function mixrgb(rgb1, rgb2, p) {
    return {
        r: Math.round(p * rgb1.r + (1 - p) * rgb2.r),
        g: Math.round(p * rgb1.g + (1 - p) * rgb2.g),
        b: Math.round(p * rgb1.b + (1 - p) * rgb2.b)
    };
}

/*
 * Converts hex encoded color value to rgb
 *
 * @param {string} hex - hex color value
 * @return {object} - rgb color object
 */
function hexToRgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

/*
 *   Mixes RGB color with white to give a transparency effect
 *
 *   @param {hex color} hex - color to which transparency has to be applied
 *   @param {float} opacity - level of opacity (0.0 - 1.0 scale)
 *   @return {rgba string} - rgba color with new transparency
 */
function applyTransparency(hex, opacity){
    var startRgb = mixrgb(hexToRgb(hex), {r:255, g:255, b:255}, opacity);
    return 'rgba(' + startRgb.r + ',' + startRgb.g + ',' + startRgb.b + ',' + 1.0 + ')';
}


/*
 *   Formats currency
 *
 *   @param {float/int} value - number to be formatted
 *   @return {string} formatted value
 */
function formatcurrency(value) {
    if (value === undefined) {
        return "N/A";
    } else if (value >= 1000000) {
        return "$" + Math.round(value / 1000000).toString() + " M";
    } else if (value < 1000000 && value >= 1000) {
        return "$" + Math.round(value / 1000).toString() + " K";
    } else if (value < 1 && value != 0) {
        return "¢" + Math.round(value * 100).toString();
    } else {
        return "$ " + value.toString();
    }
}

/*
 *   Formats currency with no rounding
 *
 *   @param {float/int} value - number to be formatted
 *   @return {string} formatted value
 */
function formatCurrencyExact(value) {
    var commasFormatter = d3.format(",.0f")
    return "$ " + commasFormatter(value);
}

/*
 *   Formats percentage
 *
 *   @param {float/int} value - number to be formatted
 *   @return {string} formatted value
 */
function formatPercentage(value) {
    if (value > 0) {
        return "+ " + value.toString() + "%";
    } else if (value < 0) {
        return "- " + Math.abs(value).toString() + "%";
    } else {
        return Math.abs(value).toString() + "%";
    }
}

export default {
    ie: ie,
    findHash: findHash,
    stopPropagation: stopPropagation,
    applyTransparency: applyTransparency,
    formatcurrency: formatcurrency,
    formatCurrencyExact: formatCurrencyExact,
    formatPercentage: formatPercentage
};
