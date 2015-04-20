var DatasetStatusConstants = require('../constants/DatasetStatusConstants');
var DatasetStatus = DatasetStatusConstants.DatasetStatus;
var BudgetAppConstants = require('../constants/BudgetAppConstants');
var ActionTypes = BudgetAppConstants.ActionTypes;
var dispatcher = require('../dispatcher/BudgetAppDispatcher');

function DataProvider(id, datasets, name) {
    this.id = id;
    this.name = name;
    this.version = -1;
    this.raw = []; // array of datasets
    this.initializationParameters = null;
    this.consumerReady = false;
    this.categories = null;

    this.data = null;

    this.status = DatasetStatus.DS_STATE_READY;
    for (var i=0; i<datasets.length; ++i) {
        this.raw.push(datasets[i]);
        if (! datasets[i].isReady()) this.status = DatasetStatus.DS_STATE_PENDING;
        if (datasets[i].version > this.version) this.version = datasets[i].version;
    }

    this.setInitializer = function(initialCommands = null) {
        if (initialCommands != null) this.initializationParameters = initialCommands;
    };

    this.getVersion = function() {
        return this.version;
    };

    this.prepareData = function() {
        for (var i=0; i<this.raw.length; ++i) {
            var ds = this.raw[i];
            if (! ds.isReady() && ! ds.isRequested()) {
                var source =GBEVars.apiPath + "/datasets/" + ds.serverId;
                $.get( source, function( r ) {
                }).done(this.receiveData).fail(this.receiveError);
                ds.setRequested();
            }
        }
        this.consumerReady = true;
    };

    this.receiveData = function (r) {
        for (var i=0; i<r.data.length; ++i) {
            dispatcher.dispatch({
                actionType: ActionTypes.DATASET_RECEIVED,
                payload: r.data[i]
            });
        }
    };

    this.receiveError = function(r) {
        console.log("ERROR - failed to get the data: " + JSON.stringify(r));
    };

    this.updatedData = function () {
        this.status = DatasetStatus.DS_STATE_READY;
        for (var i=0; i<datasets.length; ++i) {
            if (! datasets[i].isReady()) this.status = DatasetStatus.DS_STATE_PENDING;
            if (datasets[i].version > this.version) this.version = datasets[i].version;
        }
        if (this.consumerReady && this.isReady()) {
            this.categories = this.raw[0].data.categoryIdentifiers;
            this.initialize();
        }
    };

    this.datasetCompare = function(ds1, ds2) {
        var result = ds1.data.year - ds2.data.year;
        if (result == 0) {
            result = ds1.data.month - ds2.data.month;
            if (result == 0) {
                result = ds1.data.day - ds2.data.day;
            }
        }
        return result;
    };

    this.initialize = function () {
        console.log("Initializing provider " + this.name);
        var hierarchy = this.initializationParameters.hierarchy;
        var nCategories = hierarchy.length;
        var nPeriods = this.raw.length;
        var accountTypes = this.initializationParameters.accountTypes;
        var amountThreshold = 0.0;

        if ('amountThreshold' in this.initializationParameters) {
            amountThreshold = +this.initializationParameters.amountThreshold;
        }
        if (this.raw.length > 1) this.raw.sort(this.datasetCompare);
        /*
         * We need to do a pass to:
         *  - Merge datasets from multiple periods
         *  - Aggregate over categories that are not explicitly included in the hierarchy
         * We'll do the merge/aggregation using a tree.
         */
        this.count = 0;
        var tree = {};
        for (var iPeriod = 0; iPeriod < this.raw.length; ++iPeriod) {
            var data = this.raw[iPeriod].data;
            console.log("Processing period " + iPeriod + ": " + data.name);
            // First we need to map the requested categories to those in the dataset
            var catMap = new Array(nCategories);
            for (var iCat = 0; iCat < nCategories; ++iCat) {
                catMap[iCat] = data.categoryIdentifiers.indexOf(hierarchy[iCat]);
                if (catMap[iCat] < 0) {
                    throw "Unable to map category " + hierarchy[iCat] + " in dataset " + data.name;
                }
            }

            for (var j = 0; j < data.items.length; ++j) {
                var item = data.items[j];
                if (accountTypes.indexOf(item.type) < 0) continue;
                var current = tree;
                for (var level = 0; level < nCategories; ++level) {
                    if (!(item.categories[catMap[level]] in current)) current[item.categories[catMap[level]]] = {};
                    current = current[item.categories[catMap[level]]];
                }
                if (!(item.account in current)) {
                    current[item.account] = {
                        account: item.account,
                        accountType: item.type,
                        categories: item.categories,
                        amount: new Array(nPeriods)
                    };
                    for (var k = 0; k < nPeriods; ++k) current[item.account].amount[k] = 0.0;
                    this.count++;
                }
                current[item.account].amount[iPeriod] += (+item.amount);
            }
        }
        console.log("Pre-threshold count: " + this.count);

        // Now collapse the tree back out
        this.data = this.collapseTree (tree, 0, nCategories, amountThreshold);
        console.log("Final count: " + this.data.length);
    };

    this.collapseTree = function (node, currentLevel, nLevels, threshold) {
        var data = [];
        if (currentLevel == nLevels) {
            for (var acct in node) {
                if (node.hasOwnProperty(acct)) {
                    var keep = false;

                    for (var i=0; !keep && i<node[acct].amount.length; ++i) {
                        var amt = node[acct].amount[i];

                        if (Math.abs(amt) >= threshold) keep = true;
                    }
                    if (keep) data.push(node[acct]);
                }
            }
        }
        else {
            for (var prop in node) {
                if (node.hasOwnProperty(prop)) {
                    data = data.concat(this.collapseTree(node[prop], currentLevel + 1, nLevels, threshold));
                }
            }
        }
        return data;
    };

    this.isReady = function() {
        return (this.status == DatasetStatus.DS_STATE_READY);
    };

    this.getData = function (commands) {
        if (this.status == DatasetStatus.DS_STATE_READY) {
            return {
                getArray: function () {
                    return [];
                }
            };
        }
        else {
            return {
                getArray: function () {
                    return null;
                }
            };
        }
    };
}

module.exports = DataProvider;
