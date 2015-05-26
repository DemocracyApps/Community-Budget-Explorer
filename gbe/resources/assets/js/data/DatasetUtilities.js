var assign = require('object-assign');

var DatasetStatus = require('../constants/DatasetStatus');
var ActionTypes = require('../constants/ActionTypes');
var dispatcher = require('../common/BudgetAppDispatcher');
var AccountTypes = require('../constants/AccountTypes');

var datasetStore = require('../stores/DatasetStore');

var DatasetUtilities = {
    /*
     * There are a few weirdnesses in the API-delivered data that we need to handle here.
     * It's likely we actually want to change this on the server side, but for now we'll
     * handle it here:
     *
     *  - Reverse sign on all revenue values - this is an artifact of the accounting system.
     *    We should actually do this for other accounts similarly affected as well, but for
     *    now I'll just assume nobody's looking at anything other than expense and revenue.
     *
     *  - Treat 'Account' as just another category. The incoming data has a 'categories' field
     *    with the array of categories plus a separate 'account' field. We want interface to be
     *    uniform for DataProvider users, so here we'll treat it as if the categories array
     *    is just one longer with the acccount in the last slot.
     */

    mergeDatasets: function (rawDatasets, parameters) {

        var hierarchy = parameters.hierarchy;
        var accountTypes = parameters.accountTypes;
        var amountThreshold = parameters.amountThreshold;
        var nPeriods = rawDatasets.length;

        if (rawDatasets.length > 1) rawDatasets.sort(this.datasetCompare);

        /* Now set up a couple utility functions to use below */
        var mapCategories = function (desiredHierarchy, apiData) {
            let catMap = new Array(desiredHierarchy.length);
            for (let i = 0; i < desiredHierarchy.length; ++i) {
                catMap[i] = apiData.categoryIdentifiers.indexOf(hierarchy[i]);
                if (catMap[i] < 0) {
                    if (hierarchy[i] == 'Account') {
                        catMap[i] = -1;
                    }
                    else {
                        throw "Unable to map category " + hierarchy[i] + " in dataset " + data.name;
                    }
                }
            }
            return catMap;
        };

        var getCurrentCategory = function (level, item, categoryMap) {
            return (categoryMap[level] >= 0) ? item.categories[categoryMap[level]] : item.account;
        };
        /*
         * We need to do a pass to:
         *  - Merge datasets from multiple periods
         *  - Aggregate over categories that are not explicitly included in the hierarchy
         * We'll do the merge/aggregation using a tree.
         */
        this.count = 0;
        var tree = {isBottom: false};

        for (let iPeriod = 0; iPeriod < rawDatasets.length; ++iPeriod) {
            let data = rawDatasets[iPeriod].data;
            if (!rawDatasets[iPeriod].isReady()) continue;
            /*
             * First we need to map the requested categories to those in the dataset
             * The catMap array will contain, for each category requested, its index in
             * the array of categories in the dataset, or -1 for Account, since that is
             * treated specially in the data we get from the API.
             *
             */
            let categoryMap = mapCategories(hierarchy, data);

            for (let j = 0; j < data.items.length; ++j) {
                let level;
                let item = data.items[j];
                item.amount = Number(item.amount);

                if (accountTypes.indexOf(item.type) < 0) continue; // Skip if not one of the specified account types

                if (!(item.type in tree)) tree[item.type] = {isBottom: false};

                let current = tree[item.type]; // We never aggregate across account types
                let key;
                /*
                 * Build the tree up to, but not including the last level
                 */
                for (level = 0; level < hierarchy.length - 1; ++level) {
                    key = getCurrentCategory(level, item, categoryMap);
                    if (!(key in current)) current[key] = {isBottom: false};
                    current = current[key];
                }

                key = getCurrentCategory(hierarchy.length - 1, item, categoryMap);
                if (!(key in current)) {
                    let amounts = new Array(nPeriods);
                    for (var k = 0; k < nPeriods; ++k) amounts[k] = Number(0.0);
                    let categories = new Array(hierarchy.length);
                    for (level = 0; level < hierarchy.length; ++level) {
                        categories[level] = getCurrentCategory(level, item, categoryMap);
                    }

                    current[key] = {
                        isBottom: true,
                        accountType: item.type,
                        categories: categories,
                        amount: amounts
                    };
                    this.count++;
                }

                let factor = (item.type == AccountTypes.REVENUE) ? -1.0 : 1.0;
                current[key].amount[iPeriod] += Number(item.amount) * factor;
            }
        }

        // Now collapse the tree back out
        let mergedData = [];
        for (var accType in accountTypes) {
            var partial = this.extractFromTree(tree[accountTypes[accType]], +amountThreshold);
            mergedData = mergedData.concat(partial);
        }
        return mergedData;
    },

    extractFromTree: function (node, threshold) {
        var data = [];

        if (node.hasOwnProperty('isBottom')) {
            if (node.isBottom) {
                var keep = false;
                for (let i = 0; !keep && i < node.amount.length; ++i) {
                    if (Math.abs(node.amount[i]) >= threshold) keep = true;
                }
                if (keep) data.push(node);
            }
            else {
                for (var prop in node) {
                    if (prop != 'isBottom' && node.hasOwnProperty(prop)) {
                        data = data.concat(this.extractFromTree(node[prop], threshold));
                    }
                }
            }
        }
        return data;
    },

    datasetCompare: function(ds1, ds2) {
        if (ds1.data == null || ds2.data == null) {
            result = (ds1.data==null)?1:-1;
        }
        else {
            var result = ds1.data.year - ds2.data.year;
            if (result == 0) {
                result = ds1.data.month - ds2.data.month;
                if (result == 0) {
                    result = ds1.data.day - ds2.data.day;
                }
            }
        }
        return result;
    },

    formatDollarAmount: function formatDollarAmount(x) {
        x = Math.round(x);
        let prefix = '$';
        if (x < 0.) prefix = '-$';
        x = Math.abs(x);
        let val = prefix + x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        return val;
    },

    computeChanges: function computeChanges (item, index) {
        let length = item.amount.length;
        let useInfinity = false;
        if (length < 2) throw "Minimum of 2 datasets required for ChangeExplorer";
        let cur = item.amount[length-1], prev = item.amount[length-2];
        item.difference = cur-prev;
        if (Math.abs(prev) < 0.001) {
            if (useInfinity) {
                item.percent = String.fromCharCode(8734) + " %";
            }
            else {
                item.percent = "New";
            }
            item.percentSort = 10000 * Math.abs(item.difference);
        }
        else if (cur < 0. || prev < 0.) {
            item.percent="N/A";
            item.percentSort = 10000 * Math.abs(item.difference);
        }
        else {
            let pct = Math.round(1000*(item.difference)/prev)/10;
            item.percent = (pct) + "%";
            item.percentSort = Math.abs(item.percent);
        }
    },

    sortByAbsolutePercentage: function sortByAbsolutePercentage () {
        return item2.percentSort - item1.percentSort;
    },


    sortByAbsoluteDifference: function sortByAbsoluteDifference(item1, item2) {
        var result = Math.abs(item2.difference) - Math.abs(item1.difference);
        return result;
    }
};


module.exports = DatasetUtilities;

