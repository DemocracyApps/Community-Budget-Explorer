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

var MainCardStore = assign({}, EventEmitter.prototype, {

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
            return (this.dataObjects[id].version > version);
        }
        return false;
    },

    getData: function (id) {
        var data = null;
        if (id >= 0 && id < this.dataObjects.length) {
            var object = this.dataObjects[id];
            if (object.status == STATE_READY) {
                data = object.data;
            }
            else if (object.status == STATE_NEW) {
                console.log("Holy holidays, I'm going to download dataset " + object.datasetId);
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

dispatcher.register(function (action) {
    switch (action.actionType)
    {
        case ActionTypes.INIT_CARD_STORE:
            MainCardStore.emitChange()
            break;

        default:
        // no op
    }
});

module.exports = MainCardStore;
