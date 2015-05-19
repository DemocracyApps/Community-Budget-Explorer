var DatasetStatus = require('../constants/DatasetStatus');
var ActionTypes = require('../constants/ActionTypes');
var dispatcher = require('../common/BudgetAppDispatcher');
var AccountTypes = require('../constants/AccountTypes');

var datasetStore = require('../stores/DatasetStore');
var datasetUtilities = require('./DatasetUtilities');


function DataModel(id, datasetIds, initialCommands = null) {
    this.id = id;
    this.timestamp = -1;
    this.readyCount= 0;
    this.rawDatasets = []; // array of datasets
    this.initializationParameters = null;
    this.currentCommands = null;
    this.data = null;

    for (let i=0; i<datasetIds.length; ++i) {
        var ds = datasetStore.getDataset(datasetIds[i]);
        this.rawDatasets.push(ds);
    }

    if (initialCommands != null) this.initializationParameters = initialCommands;

    this.checkReady = function () {
        let needsUpdate = false;
        this.status = DatasetStatus.DS_STATE_READY;
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
            hierarchy:this.initializationParameters.hierarchy,
            accountTypes:this.initializationParameters.accountTypes,
            amountThreshold:amountThreshold
        });
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
        if (this.readyCount > 0) {
            this.data = null;

            let amountThreshold = 0.0;
            if ('amountThreshold' in this.initializationParameters) {
                amountThreshold = Number(this.initializationParameters.amountThreshold);
            }
            this.data = datasetUtilities.mergeDatasets(this.rawDatasets, {
                hierarchy:this.initializationParameters.hierarchy,
                accountTypes:this.initializationParameters.accountTypes,
                amountThreshold:amountThreshold
            });
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
                if (startLevel + nLevels > this.initializationParameters.hierarchy.length) {
                    nLevels = this.initializationParameters.hierarchy.length - startLevel;
                }
            }
            let headers = this.getHeaders();

            return {
                categories:this.initializationParameters.hierarchy,
                dataHeaders:headers,
                levelsDown: startLevel,
                levelsAggregated: this.initializationParameters.hierarchy.length - nLevels - startLevel,
            };
        }
        else {
            return null;
        }
    };

    this.getData = function getData (commands, partialOk=false) {
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
                if (startLevel + nLevels > this.initializationParameters.hierarchy.length) {
                    nLevels = this.initializationParameters.hierarchy.length - startLevel;
                }
            }
            console.log("startPath = " + startPath + ",  startLevel = " + startLevel);
            console.log("nLevels = " + nLevels);

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

            return {
                categories:this.initializationParameters.hierarchy,
                dataHeaders:headers,
                levelsDown: startLevel,
                levelsAggregated: this.initializationParameters.hierarchy.length - nLevels - startLevel,
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
}

module.exports = DataModel;
