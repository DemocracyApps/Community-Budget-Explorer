var dispatcher = require('../common/BudgetAppDispatcher');
var EventEmitter = require('events').EventEmitter;

var assign = require('object-assign');

var ActionTypes = require('../constants/ActionTypes');

var DataModel = require('../data/DataModel');

var CHANGE_EVENT = 'change';

var DataModelStore = assign({}, EventEmitter.prototype, {

    modelIdCounter: 0,
    _models: [],
    dependencyMap: [],

    createModel: function createModel (inputDatasets, initialization) {
        var dm = new DataModel(this.modelIdCounter++, inputDatasets, initialization);
        inputDatasets.forEach(function (datasetId) {
           this.addDependency(datasetId, dm.id);
        }.bind(this));
        this._models[dm.id] = dm;
        return dm;
    },

    deleteModel: function deleteModel(id) {
        delete this._models[id];
    },

    getModel: function getModel(id) {
        return this._models[id];
    },

    addDependency: function (datasetId, modelId) {
        if (! (datasetId in this.dependencyMap) ) this.dependencyMap[datasetId] = [];
        this.dependencyMap[datasetId].push(modelId);
    },

    emitChange: function() {
        this.emit(CHANGE_EVENT);
    },
    /**
     * @param {function} callback
     */
    addChangeListener: function(callback) {
        this.on(CHANGE_EVENT, callback);
    },

    /**
     * @param {function} callback
     */
    removeChangeListener: function(callback) {
        this.removeListener(CHANGE_EVENT, callback);
    }
});

dispatcher.register(function (action) {
    switch (action.actionType)
    {
        default:
        // no op
    }
});

module.exports = DataModelStore;
