var DatasetStatus = require('../constants/DatasetStatus');
var ActionTypes = require('../constants/ActionTypes');
var dispatcher = require('../common/BudgetAppDispatcher');
var AccountTypes = require('../constants/AccountTypes');

var datasetStore = require('../stores/DatasetStore');


function DataModel(id, datasetIds, initialCommands = null) {
    this.id = id;
    this.timestamp = -1;
    this.raw = []; // array of datasets
    this.initializationParameters = null;
    this.categories = null;
    this.categoryMap = null;
    this.currentCommands = null;

    this.data = null;
    if (initialCommands != null) this.initializationParameters = initialCommands;

    this.status = DatasetStatus.DS_STATE_READY;
    var readyCount = 0;

    for (var i=0; i<datasetIds.length; ++i) {
        var ds = datasetStore.getDataset(datasetIds[i]);
        this.raw.push(ds);
        if (ds.isReady()) ++readyCount;
        if (ds.getTimestamp() > this.timestamp) this.timestamp = ds.getTimestamp();
    }
    if (readyCount < datasetIds.length) {
        this.status = (readyCount == 0?DatasetStatus.DS_STATE_PENDING:DatasetStatus.DS_STATE_PARTIAL);
    }

    this.getTimestamp = function() {
        return this.timestamp;
    };

    this.commandsChanged = function commandsChanged(commands) {
        if (JSON.stringify(this.currentCommands) === JSON.stringify(commands)) {
            return false;
        }
        return true;
    };

    this.dataChanged = function dataChanged () {
        // We only know if the data has changed if a component asks
        var needUpdate = false;
        readyCount = 0;
        var firstReady = -1;
        for (var i=0; i<this.raw.length; ++i) {
            if (this.raw[i].isReady()) {
                ++readyCount;
                if (firstReady < 0) firstReady = i;
            }
            if (this.raw[i].timestamp > this.timestamp) {
                this.timestamp = this.raw[i].timestamp;
                needUpdate = true;
            }
        }
        this.status = DatasetStatus.DS_STATE_READY;
        if (readyCount < datasetIds.length) {
            this.status = (readyCount == 0?DatasetStatus.DS_STATE_PENDING:DatasetStatus.DS_STATE_PARTIAL);
        }
        /*
         * TODO: Need a better way to deal with categories across (and within) datasets. This assumes they are uniform.
         */
        if (readyCount > 0) {
            this.data = null;
            this.categories = this.raw[firstReady].data.categoryIdentifiers;
            this.initialize();
        }
        return needUpdate;
    };

    this.datasetCompare = function(ds1, ds2) {
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
    };

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
    this.initialize = function () {
        var hierarchy = this.initializationParameters.hierarchy;
        var nPeriods = this.raw.length;
        var accountTypes = this.initializationParameters.accountTypes;
        var amountThreshold = 0.0;

        if ('amountThreshold' in this.initializationParameters) {
            amountThreshold = Number(this.initializationParameters.amountThreshold);
        }
        if (this.raw.length > 1) this.raw.sort(this.datasetCompare);

        /* Now set up a couple utility functions to use below */
        var mapCategories = function(desiredHierarchy, apiData) {
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

        var getCurrentCategory = function(level, item, categoryMap) {
             return (categoryMap[level] >= 0)?item.categories[categoryMap[level]]:item.account;
        };
        /*
         * We need to do a pass to:
         *  - Merge datasets from multiple periods
         *  - Aggregate over categories that are not explicitly included in the hierarchy
         * We'll do the merge/aggregation using a tree.
         */
        this.count = 0;
        this.altCount = 0;
        this.nzCount = 0;
        var tree = {isBottom:false};

        for (let iPeriod = 0; iPeriod < this.raw.length; ++iPeriod) {
            let data = this.raw[iPeriod].data;
            if (! this.raw[iPeriod].isReady()) continue;
            /*
             * First we need to map the requested categories to those in the dataset
             * The catMap array will contain, for each category requested, its index in
             * the array of categories in the dataset, or -1 for Account, since that is
             * treated specially in the data we get from the API.
             *
             */
            let categoryMap = mapCategories(hierarchy, data);

            console.log("Incoming dataset for period " + iPeriod + " is " + data.items.length);
            for (let j = 0; j < data.items.length; ++j) {
                let level;
                let item = data.items[j];
                item.amount = Number(item.amount);

                if (accountTypes.indexOf(item.type) < 0) continue; // Skip if not one of the specified account types

                if (! (item.type in tree)) tree[item.type] = {isBottom:false};

                let current = tree[item.type]; // We never aggregate across account types
                let key;
                /*
                 * Build the tree up to, but not including the last level
                 */
                for (level = 0; level < hierarchy.length - 1; ++level) {
                    key = getCurrentCategory(level, item, categoryMap);
                    if (!(key in current)) current[key] = {isBottom:false};
                    current = current[key];
                }

                key = getCurrentCategory(hierarchy.length-1, item, categoryMap);
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
                else {
                    this.altCount++;
                }
                if (Math.abs(item.amount) > 0.0) ++this.nzCount;
                let factor = (item.type == AccountTypes.REVENUE)?-1.0:1.0;
                current[key].amount[iPeriod] += Number(item.amount) * factor;
            }
        }
        console.log("Non-zero count = " + this.nzCount);
        console.log("The counts before extraction are " + this.count + ", " + this.altCount);

        // Now collapse the tree back out
        this.data = [];
        for (var accType in accountTypes) {
            var partial = this.extractFromTree (tree[accountTypes[accType]], amountThreshold);
            this.data = this.data.concat(partial);
        }
        console.log("Final data length is " + this.data.length);
    };

    this.extractFromTree = function (node, threshold) {
        var data = [];

        if (node.hasOwnProperty('isBottom')) {
            if (node.isBottom) {
                var keep = false;
                for (i = 0; !keep && i < node.amount.length; ++i) {
                    if (Math.abs(node.amount[i]) >= threshold) keep = true;
                }
                keep = true;
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
    };

    this.isReady = function() {
        return (this.status == DatasetStatus.DS_STATE_READY);
    };

    this.getHeaders = function getHeaders() {
        var headers = [];
        for (var i=0; i<this.raw.length; ++i) {
            if (this.raw[i].data != null) headers.push(this.raw[i].data.year + "");
        }
        return headers;
    };


    this.pathMatches = function pathMatches(template, path) {
        var keep = true;
        for (let i=0; keep && i<template.length; ++i) {
            if (template[i] != null && template[i] != path[i]) keep = false;
        }
        return keep;
    };

    this.getData = function (commands, partialOk=false) {
        if (this.status == DatasetStatus.DS_STATE_READY ||
            (this.status == DatasetStatus.DS_STATE_PARTIAL && partialOk)) {
            this.currentCommands = commands;

            var data = [];
            var accountTypes = null;
            var startPath = null;
            var startLevel = 0;
            var nLevels = 1000;

            // Basic order is: (1) filters, including startPath, (2) reduce

            if ('accountTypes' in commands) accountTypes = commands.accountTypes;
            if ('startPath' in commands) {
                startPath = commands.startPath;
                if (startPath != null) startLevel = startPath.length;
            }
            if ('nLevels' in commands) {
                nLevels = commands.nLevels;
                if (startLevel + nLevels > this.initializationParameters.hierarchy.length) {
                    nLevels = this.initializationParameters.hierarchy.length - startLevel;
                }
            }
            console.log("The start path is " + startPath);
            console.log(" and the starting data length is " + this.data.length);
            /* Filters
             * What want to do is include only those that
             *    a. have a type in accountTypes (or accountTypes is null)
             *    b. match the startPath
             * then we want to traverse the remaining path for maxLevels and copy over
             */
            var tree = {};
            for (let i=0; i<this.data.length; ++i) {
                let item = this.data[i];

                // See if it's an included account type
                if (accountTypes == null || accountTypes.indexOf(item.accountType)>=0) {
                    // Now see if it matches startPath
                    if (startPath == null || this.pathMatches(startPath, item.categories)) {
                        // Ok, we're taking it, up to maxlevels deep from the end of startPath

                        if (! (item.type in tree)) tree[item.type] = {isBottom:false};
                        var current = tree[item.type]; // We never aggregate across account types
                        /*
                         * Build the tree from startLevel up to, but not including startLevel+maxLevels
                         */
                        for (let i = 0; i < nLevels; ++i) {
                            let key = item.categories[startLevel+i];
                            if (i == nLevels-1) {
                                if (!(key in current)) {
                                    current[key] = {
                                        isBottom: true,
                                        account: key,
                                        accountType: item.type,
                                        categories: item.categories.slice(),
                                        amount: item.amount.slice()
                                    }
                                }
                                else {
                                    for (let j = 0; j < item.amount.length; ++j) {
                                        current[key].amount[j] += item.amount[j];
                                    }
                                }
                            }
                            else {
                                if (!(key in current)) current[key] = {isBottom:false};
                                current = current[key];
                            }
                        }
                    }
                }
            }
            // Now collapse the tree back out
            for (var accType in tree) {
                var partial = this.extractFromTree (tree[accType], 0.0);
                data = data.concat(partial);
            }

            let headers = this.getHeaders();
            if (false && 'reduce' in commands) {
                let reduceCmd = commands.reduce;
                if (reduceCmd == 'difference') {
                    headers = ['Difference'];
                    if (data[0].amount.length < 2) throw "Difference reduce requires 2 datasets";
                    if (data[0].amount.length > 2)
                        console.log("Warning: difference reduce applied to more than 2 datasets - using first two.");
                    for (let i=0; i<data.length; ++i) {
                        let inItem = data[i];
                        let outItem = {
                            accountType: inItem.accountType,
                            categories: inItem.categories,
                            amount: [inItem.amount[1] - inItem.amount[0]]
                        }
                        data[i] = outItem;
                    }
                }
                else {
                    throw "Reduce command " + reduceCmd + " not yet implemented.";
                }

            }
console.log("getData return data length = " + data.length);
            return {
                categories:this.initializationParameters.hierarchy,
                dataHeaders:headers,
                data: data
            };
        }
        else {
            return null;
        }
    };

    this.old_getData = function (commands, partialOk=false) {
        if (this.status == DatasetStatus.DS_STATE_READY ||
            (this.status == DatasetStatus.DS_STATE_PARTIAL && partialOk)) {
            this.currentCommands = commands;

            var data = [];
            var accountTypes = null;
            if ('accountTypes' in commands) accountTypes = commands.accountTypes;

            for (var i=0; i<this.data.length; ++i) {
                var item = this.data[i];
                if (accountTypes == null || accountTypes.indexOf(item.accountType)>=0) {
                    data.push(item);
                }
            }
            return {
                categories:this.initializationParameters.hierarchy,
                dataHeaders:this.getHeaders(),
                data: data
            };
        }
        else {
            return null;
        }
    };
}

module.exports = DataModel;
