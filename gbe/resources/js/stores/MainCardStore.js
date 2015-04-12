var dispatcher = require('../dispatcher/BudgetAppDispatcher');
var EventEmitter = require('events').EventEmitter;

var assign = require('object-assign');

var BudgetAppConstants = require('../constants/BudgetAppConstants');
var ActionTypes = BudgetAppConstants.ActionTypes;

var CHANGE_EVENT = 'change';
var _cards = {};

var MainCardStore = assign({}, EventEmitter.prototype, {

    idCounter: 0,

    versionCounter: 1, // Let's components optimize whether they need to redraw

    dataObjects: [],

    // Various local things.

    sayHi: function() {
        console.log("Hi!");
    },

    importCard: function (data) {
        console.log("CardStore is importing " + JSON.stringify(data));
        var item = {};
        item.data = data;
        item.version = this.versionCounter++;
        this.dataObjects[this.idCounter] = item;
        this.emit(CHANGE_EVENT);
        return this.idCounter++;
    },

    dataHasUpdated: function (id, version) {
        if (id >= 0 && id < this.dataObjects.length) {
            return (this.dataObjects[id].version > version);
        }
        return false;
    },

    getData: function (id) {
        if (id >= 0 && id < this.dataObjects.length) {
            return this.dataObjects[id];
        }
        return null;
    },

    getDataIfUpdated: function (id, version) {
        if (this.dataHasUpdated(id,version)) {
            return this.getData(id);
        }
        return null;
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
        case ActionTypes.INIT_CARD_STORE:
            console.log("Got case 1");
            MainCardStore.emitChange()
            break;

        default:
        // no op
    }
});

module.exports = MainCardStore;
