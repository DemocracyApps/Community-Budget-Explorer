var dispatcher = require('../dispatcher/BudgetAppDispatcher');
var EventEmitter = require('events').EventEmitter;

var assign = require('object-assign');

var BudgetAppConstants = require('../constants/BudgetAppConstants');
var ActionTypes = BudgetAppConstants.ActionTypes;

var DatasetStatusConstants = require('../constants/DatasetStatusConstants');
var DatasetStatus = DatasetStatusConstants.DatasetStatus;

var Dataset = require('../data/Dataset');
var DataProvider = require('../data/DataProvider');

var DS_CHANGE_EVENT = 'ds_change';

var MainDatasetStore = assign({}, EventEmitter.prototype, {

    versionCounter: 1, // Let's components optimize whether they need to redraw

    datasetIdCounter: 0,

    datasets: [], // These are the datasets as received from the server

    dataProviderIdCounter: 0,

    dataProviders: [], // These are the objects that components will actually operate with.

    serverIdMap: {},

    dependencyMap: {},

    /*
     * The serverId is the ID of the dataset on the server. This is unique as
     * long as we are only dealing with one server, so why the localId? This is
     * to prepare to allow aggregation of datasets from multiple sources, when
     * the serverId may no longer be unique. We can deal with the additional
     * complexity here without anything changing outside the store.
     */
    registerDataset: function (serverId, name = "Unnamed") {
        console.log("Registering dataset " + name);
        var ds = null;
        if (serverId in this.serverIdMap) {
            ds = this.datasets[this.serverIdMap[serverId]];
        }
        else {
            ds = new Dataset(this.versionCounter++, this.datasetIdCounter++, serverId);
            this.serverIdMap[serverId] = ds.localId;
            this.datasets[ds.localId] = ds;
        }
        var dp = new DataProvider(this.dataProviderIdCounter++, [ds], name);
        this.dataProviders[dp.id] = dp;
        this.addDependency(ds.localId, dp.id);
        return dp.id;
    },

    addDependency: function (dsId, providerId) {
        if (! (dsId in this.dependencyMap) ) this.dependencyMap[dsId] = [];
        this.dependencyMap[dsId].push(providerId);
    },

    registerDatasetCollection: function (localIds, name = "Unnamed") {
        var dsArray = [];
        for (var i=0; i<localIds.length; ++i) dsArray.push(this.datasets[localIds[i]]);

        var dp = new DataProvider(this.dataProviderIdCounter++, dsArray, name);
        this.dataProviders[dp.id] = dp;

        for (var i=0; i<localIds.length; ++i) this.addDependency(localIds[i], dp.id);
        return dp.id;
    },

    getDataProvider: function(id) {
        return this.dataProviders[id];
    },

    emitChange: function() {
        this.emit(DS_CHANGE_EVENT);
    },
    /**
     * @param {function} callback
     */
    addChangeListener: function(callback) {
        this.on(DS_CHANGE_EVENT, callback);
    },

    /**
     * @param {function} callback
     */
    removeChangeListener: function(callback) {
        this.removeListener(DS_CHANGE_EVENT, callback);
    }
});

MainDatasetStore.dispatchToken = dispatcher.register(function (action) {
    switch (action.actionType)
    {
        case ActionTypes.DATASET_RECEIVED:
            var dsId = MainDatasetStore.serverIdMap[action.payload.id];

            console.log("DATASET_RECEIVED - ID = " + dsId + " corresponding to server ID = " + action.payload.id);

            var ds = MainDatasetStore.datasets[dsId];
            ds.receiveDataset(action.payload, MainDatasetStore.versionCounter++);
            if (dsId in MainDatasetStore.dependencyMap) {
                for (var i=0; i<MainDatasetStore.dependencyMap[dsId].length; ++i) {
                    var id = MainDatasetStore.dependencyMap[dsId][i];
                    var dp = MainDatasetStore.dataProviders[id];
                    dp.updatedData(dsId);
                }
            }
            MainDatasetStore.emitChange()
            break;

        default:
            // Nothing
            break;
    }
});

module.exports = MainDatasetStore;
