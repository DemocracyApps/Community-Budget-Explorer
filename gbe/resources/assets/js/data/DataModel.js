var DatasetStatus = require('../constants/DatasetStatus');
var ActionTypes = require('../constants/ActionTypes');
var dispatcher = require('../common/BudgetAppDispatcher');
var AccountTypes = require('../constants/AccountTypes');

var datasetStore = require('../stores/DatasetStore');
var datasetUtilities = require('./DatasetUtilities');


function DataModel(id, datasetIds, initialCommands = null, categoryMap = null) {
    this.id = id;
    this.timestamp = -1;
    this.readyCount= 0;
    this.rawDatasets = []; // array of datasets
    this.initializationParameters = null;
    this.currentCommands = null;
    this.data = null;
    this.processedData = null;
    this.categoryMap = null;
    this.hierarchy = null;

    for (let i=0; i<datasetIds.length; ++i) {
        var ds = datasetStore.getDataset(datasetIds[i]);
        if (this.hierarchy == null) this.hierarchy = ds.categories.slice();
        this.rawDatasets.push(ds);
    }

    if (initialCommands != null) this.initializationParameters = initialCommands;

    if (categoryMap && categoryMap.translations && categoryMap.translations.length > 0) {
        let tree = {};

        for (let i=0; i<categoryMap.translations.length; ++i) {
            let current = tree;
            let t = categoryMap.translations[i];
            for (let j=0; j<t.from.length; ++j) {
                if (!(t.from[j] in current)) {
                    current[t.from[j]] = {};
                }
                current = current[t.from[j]];
            }
            current.to = t.to;
            if ('suppress' in t) current.suppress = t.suppress;
        }
        this.categoryMap =  tree;
    }

    this.checkReady = function () {
        let needsUpdate = false;
        this.status = DatasetStatus.DS_STATE_READY;
        let oldCount = this.readyCount;
        this.readyCount = 0;
        for (let i=0; i<this.rawDatasets.length; ++i) {

            if (this.rawDatasets[i].isReady()) {
                ++this.readyCount;
            }
            if (this.rawDatasets[i].timestamp > this.timestamp) {
                this.timestamp = this.rawDatasets[i].getTimestamp();
                needsUpdate = true;
            }
        }
        if (this.readyCount < this.rawDatasets.length) {
            this.status = (this.readyCount == 0?DatasetStatus.DS_STATE_PENDING:DatasetStatus.DS_STATE_PARTIAL);
        }
        if (oldCount != this.readyCount) needsUpdate = true;
        return needsUpdate;
    };

    this.checkReady();
    if (this.readyCount > 0) {
        this.data = null;

        let amountThreshold = 0.0;
        if ('amountThreshold' in this.initializationParameters) {
            amountThreshold = Number(this.initializationParameters.amountThreshold);
        }
        this.data = datasetUtilities.mergeDatasets(this.rawDatasets, {
            //hierarchy:this.initializationParameters.hierarchy,
            hierarchy:this.hierarchy,
            amountThreshold:amountThreshold,
        }, this.categoryMap);
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
        var needUpdate = this.checkReady();
        /*
         * TODO: Need a better way to deal with categories across (and within) datasets. This assumes they are uniform.
         */
        if (this.readyCount > 0 && needUpdate) {
            this.data = null;

            let amountThreshold = 0.0;
            if ('amountThreshold' in this.initializationParameters) {
                amountThreshold = Number(this.initializationParameters.amountThreshold);
            }
            this.data = datasetUtilities.mergeDatasets(this.rawDatasets, {
                //hierarchy:this.initializationParameters.hierarchy,
                hierarchy:this.hierarchy,
                amountThreshold:amountThreshold
            }, this.categoryMap);
        }
        return needUpdate;
    };

    this.getHeaders = function getHeaders() {
        var headers = [];
        for (var i=0; i<this.rawDatasets.length; ++i) {
            if (this.rawDatasets[i].data != null) headers.push(this.rawDatasets[i].data.year + "");
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

    this.checkData = function checkData(commands, partialOk=false) {
        if (this.status == DatasetStatus.DS_STATE_READY ||
            (this.status == DatasetStatus.DS_STATE_PARTIAL && partialOk)) {
            var startPath = null;
            var startLevel = 0;
            var nLevels = 1000;
            if ('startPath' in commands) {
                startPath = commands.startPath;
                if (startPath != null) startLevel = startPath.length;
            }
            if ('nLevels' in commands) {
                nLevels = commands.nLevels;
                //if (startLevel + nLevels > this.initializationParameters.hierarchy.length) {
                if (startLevel + nLevels > this.hierarchy.length) {
                    //nLevels = this.initializationParameters.hierarchy.length - startLevel;
                    nLevels = this.hierarchy.length - startLevel;
                }
            }
            let headers = this.getHeaders();

            return {
                categories:this.hierarchy,
                //categories:this.initializationParameters.hierarchy,
                dataHeaders:headers,
                periods:headers,
                levelsDown: startLevel,
                //levelsAggregated: this.initializationParameters.hierarchy.length - nLevels - startLevel,
                levelsAggregated: this.hierarchy.length - nLevels - startLevel,
            };
        }
        else {
            return null;
        }
    };

    this.getData = function getData (commands, partialOk=false) {
        this.dataChanged();
        if (this.status == DatasetStatus.DS_STATE_READY ||
            (this.status == DatasetStatus.DS_STATE_PARTIAL && partialOk)) {
            this.currentCommands = commands;

            var data = [];
            var accountTypes = null;
            var startPath = null;
            var startLevel = 0;
            var nLevels = 1000;

            /*
             * Possible incoming instructions
             *
             *  - accountTypes: array of account types to include (expense, revenue, liabilities, etc.)
             *  - startPath:    array of category values at 1 or more levels to start at a specific point
             *                  in the hierarchy, e.g., ['General Fund','Police Department'] would restrict
             *                  only to divisions and accounts under the Police department.
             *  - nLevels:      Number of hierarchy levels to explicitly include beyond the startPath point;
             *                  everything beyond that level will be aggregated up. For example, in the Asheville
             *                  data, if startPath is null and nLevels is 1, we'll get the totals for each fund,
             *                  which is the first category. if startPath is ['General Fund'] and nLevels is 2,
             *                  we'll get data for the mini-hierarchy of department+division in the General Fund, with
             *                  account-level detail aggregated up to the division.
             *  - reduce:       xxx
             */
            if ('accountTypes' in commands) accountTypes = commands.accountTypes;
            if ('startPath' in commands) {
                startPath = commands.startPath;
                if (startPath != null) startLevel = startPath.length;
            }
            if ('nLevels' in commands) {
                nLevels = commands.nLevels;
                //if (startLevel + nLevels > this.initializationParameters.hierarchy.length) {
                //    nLevels = this.initializationParameters.hierarchy.length - startLevel;
                //}
                if (startLevel + nLevels > this.hierarchy.length) {
                    nLevels = this.hierarchy.length - startLevel;
                }
            }

            /* Filters
             * What want to do is include only those that
             *    a. have a type in accountTypes (or accountTypes is null)
             *    b. match the startPath
             * then we want to copy over nLevels of the remaining path, aggregating anything deeper than that
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
                                        amount: item.amount.slice(),
                                        reduce: 0
                                    }
                                }
                                else {
                                    for (let j = 0; j < item.amount.length; ++j) {
                                        current[key].amount[j] += Number(item.amount[j]);
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
                var partial = datasetUtilities.extractFromTree (tree[accType], 0.0);
                data = data.concat(partial);
            }
            let headers = this.getHeaders();
            if ('reduce' in commands) {
                let reduceCmd = commands.reduce;
                if (reduceCmd == 'difference') {
                    if (data[0].amount.length < 2) throw "Difference reduce requires 2 datasets";
                    if (data[0].amount.length > 2)
                        console.log("Warning: difference reduce applied to more than 2 datasets - using first two.");
                    for (let i=0; i<data.length; ++i) {
                        data[i].reduce = data[i].amount[1] - data[i].amount[0];
                    }
                }
                else {
                    throw "Reduce command " + reduceCmd + " not yet implemented.";
                }

            }

            this.processedData = data;

            return {
                //categories:this.initializationParameters.hierarchy,
                categories:this.hierarchy,
                dataHeaders:headers,
                periods:headers,
                levelsDown: startLevel,
                //levelsAggregated: this.initializationParameters.hierarchy.length - nLevels - startLevel,
                levelsAggregated: this.hierarchy.length - nLevels - startLevel,
                data: data
            };
        }
        else {

            return null;
        }
    };

    this.getDataModelInfo = function getDataModelInfo() {

        return {
            nDatasets: this.rawDatasets.length,
            nCategories: this.rawDatasets
        };
    };

    this.computeChanges = function computeChanges() {
        if (this.processedData != null) {
            this.processedData.map(datasetUtilities.computeChanges);
        }
    };

    this.sortByAbsoluteChange = function sortByAbsoluteChange() {
        if (this.processedData != null) {
            this.processedData.sort(datasetUtilities.sortByAbsoluteDifference);
        }
    };

    this.getCategoryNames = function (startPath, level) {
        if (this.data == null || this.data.length == 0) return null;
        
        var rows = this.data;
        var ahash = {};
        var nYears = rows[0].amount.length;
        for (let i=0; i<rows.length; ++i) {
            if (startPath == null || this.pathMatches(startPath, rows[i].categories)) {
                let current = ahash[rows[i].categories[level]];
                if (current == undefined) {
                    current = {
                        name: rows[i].categories[level],
                        value: 0.0
                    };
                    ahash[current.name] = current;
                }
                current.value += rows[i].amount[nYears - 1];
            }
        }
        var areas = [];
        for (var nm in ahash) {
            if (ahash.hasOwnProperty(nm) && Math.abs(ahash[nm].value) > 0.0) {
                areas.push(ahash[nm]);
            }
        }
        areas = areas.sort(function(a, b) {
            return b.value - a.value;
        });
        return areas;
    };
}

module.exports = DataModel;
