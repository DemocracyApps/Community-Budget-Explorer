var dispatcher = require('../common/BudgetAppDispatcher');
var EventEmitter = require('events').EventEmitter;
var assign = require('object-assign');

var ActionTypes = require('../constants/ActionTypes');

var Dataset = require('../data/Dataset');

var DS_CHANGE_EVENT = 'ds_change';


var DatasetStore = assign({}, EventEmitter.prototype, {

    timestamp: 1, // Let's components optimize whether they need to redraw
    _datasets: [], // These are the datasets as received from the server

    registerDataset: function (sourceId) {
        var ds = null;
        if (sourceId in this._datasets) {
            ds = this._datasets[sourceId];
        }
        else {
            ds = new Dataset(this.timestamp++, sourceId);
            this._datasets[sourceId] = ds;
        }
        return ds;
    },

    getDataset: function (sourceId) {
        return this._datasets[sourceId];
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

DatasetStore.dispatchToken = dispatcher.register(function (action) {
    switch (action.actionType)
    {
        case ActionTypes.DATASET_RECEIVED:

            console.log("DATASET_RECEIVED - ID = " + action.payload.id);

            var ds = DatasetStore._datasets[action.payload.id];
            ds.receiveDataset(action.payload, DatasetStore.timestamp++);
            DatasetStore.emitChange()
            break;

        default:
            // Nothing
            break;
    }
});

module.exports = DatasetStore;
