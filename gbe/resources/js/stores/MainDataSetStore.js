var dispatcher = require('../dispatcher/BudgetAppDispatcher');
var EventEmitter = require('events').EventEmitter;

var assign = require('object-assign');

var BudgetAppConstants = require('../constants/BudgetAppConstants');
var ActionTypes = BudgetAppConstants.ActionTypes;

var DS_CHANGE_EVENT = 'ds_change';
var _cards = {};

var STATE_NEW = 1;
var STATE_REQUESTED = 2;
var STATE_READY = 3;

var MainDatasetStore = assign({}, EventEmitter.prototype, {

    idCounter: 0,

    versionCounter: 1, // Let's components optimize whether they need to redraw

    dataObjects: [],

    registerDataset: function (datasetId) {
        var item = {};
        item.datasetId = datasetId;
        item.version = this.versionCounter++;
        item.status = STATE_NEW;
        item.data = null;
        this.dataObjects[this.idCounter] = item;
        return this.idCounter++;
    },

    dataHasUpdated: function (id, version) {
        if (id >= 0 && id < this.dataObjects.length) {
            console.log("Test " + id + " versions: " + version + " versus " + this.dataObjects[id].version);
            return (this.dataObjects[id].version > version);
        }
        return false;
    },

    receiveData: function (r) {
        for (var i=0; i<r.data.length; ++i) {
            dispatcher.dispatch({
                actionType: ActionTypes.DATASET_RECEIVED,
                payload: r.data[i]
            });
        }
    },

    receiveError: function(r) {
        console.log("ERROR - failed to get the data: " + JSON.stringify(r));
    },

    getData: function (id) {
        var data = null;
        if (id >= 0 && id < this.dataObjects.length) {
            var object = this.dataObjects[id];
            if (object.status == STATE_READY) {
                data = object.data;
            }
            else if (object.status == STATE_NEW) {
                var source =GBEVars.apiPath + "/datasets/" + object.datasetId;
                console.log("Download dataset " + object.datasetId + " from: " + source);
                $.get( source, function( r ) {
                }).done(this.receiveData).fail(this.receiveError);

                object.status = STATE_REQUESTED;
            }
        }
        return data;
    },

    getDataIfUpdated: function (id, version) {
        if (this.dataHasUpdated(id,version)) {
            return this.getData(id);
        }
        console.log(" ... and return");
        return null;
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
            console.log("I got a dataset - name is " + action.payload.name);
            var dsId = action.payload.id;
            for (var j=0; j<MainDatasetStore.dataObjects.length; ++j) {
                if (MainDatasetStore.dataObjects[j].datasetId == dsId) {
                    MainDatasetStore.dataObjects[j].data = action.payload;
                    MainDatasetStore.dataObjects[j].version = MainDatasetStore.versionCounter++;
                    MainDatasetStore.dataObjects[j].status = STATE_READY;
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
