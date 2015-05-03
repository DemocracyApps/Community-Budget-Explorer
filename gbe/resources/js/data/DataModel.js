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
        var nCategories = hierarchy.length;
        var nPeriods = this.raw.length;
        var accountTypes = this.initializationParameters.accountTypes;
        var amountThreshold = 0.0;
        var iPeriod, iCat, level, i, j;

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
        for (iPeriod = 0; iPeriod < this.raw.length; ++iPeriod) {
            var data = this.raw[iPeriod].data;
            if (! this.raw[iPeriod].isReady()) continue;
            // First we need to map the requested categories to those in the dataset
            var catMap = new Array(nCategories+1);
            for (iCat = 0; iCat < nCategories; ++iCat) {
                catMap[iCat] = data.categoryIdentifiers.indexOf(hierarchy[iCat]);
                if (catMap[iCat] < 0) {
                    if (hierarchy[iCat] == 'Account') {
                        catMap[iCat] = -1;
                    }
                    else {
                        throw "Unable to map category " + hierarchy[iCat] + " in dataset " + data.name;
                    }
                }
            }

            for (j = 0; j < data.items.length; ++j) {
                var item = data.items[j];
                item.amount = Number(item.amount);

                if (accountTypes.indexOf(item.type) < 0) continue;

                var current = tree;
                var key;
                /*
                 * Build the tree up to, but not including the last level
                 */
                for (level = 0; level < nCategories - 1; ++level) {
                    key = (catMap[level] >= 0)?item.categories[catMap[level]]:'Account';
                    if (!(key in current)) current[key] = {};
                    current = current[key];
                }
                level = nCategories - 1;
                key = (catMap[level] >= 0)?item.categories[catMap[level]]:'Account';

                if (!(key in current)) {
                    var amounts = new Array(nPeriods);
                    for (var k = 0; k < nPeriods; ++k) amounts[k] = Number(0.0);
                    var categories = new Array(nCategories);
                    for (var level = 0; level < nCategories; ++level) {
                        categories[level] = (catMap[level] >= 0)?item.categories[catMap[level]]:item.account;
                    }

                    current[key] = {
                        account: categories[nCategories-1],
                        accountType: item.type,
                        categories: categories,
                        amount: amounts
                    };
                    this.count++;
                }
                var factor = (item.type == AccountTypes.REVENUE)?-1.0:1.0;
                current[key].amount[iPeriod] += item.amount * factor;
            }
        }

        // Now collapse the tree back out
        this.data = this.collapseTree (tree, 0, nCategories, amountThreshold);
    };

    this.collapseTree = function (node, currentLevel, nLevels, threshold) {
        var data = [];
        if (currentLevel == nLevels-1) {
            for (var acct in node) {
                if (node.hasOwnProperty(acct)) {
                    var keep = false;

                    for (i=0; !keep && i<node[acct].amount.length; ++i) {
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

    this.getHeaders = function getHeaders() {
        var headers = [];
        for (var i=0; i<this.raw.length; ++i) {
            if (this.raw[i].data != null) headers.push(this.raw[i].data.year + "");
        }
        return headers;
    };

    this.getData = function (commands, partialOk=false) {
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
            return data;
        }
        else {
            return null;
        }
    };
}

module.exports = DataModel;
