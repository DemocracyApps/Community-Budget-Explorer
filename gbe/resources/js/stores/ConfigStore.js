var dispatcher = require('../common/BudgetAppDispatcher');
var EventEmitter = require('events').EventEmitter;

var assign = require('object-assign');

var BudgetAppConstants = require('../constants/BudgetAppConstants');
var ActionTypes = BudgetAppConstants.ActionTypes;

var CHANGE_EVENT = 'change';

var ConfigStore = assign({}, EventEmitter.prototype, {

    /*
     * We're expecting sites, pages
     */
    areas: {},

    createSection: function(areaName) {
        if ((this.areas.hasOwnProperty(areaName))) throw "createArea - " + areaName + " already exists";
        this.areas[areaName] = {
            name: areaName,
            items: {}
        };

    },

    storeConfiguration: function (areaName, key, value) {
        if (!(this.areas.hasOwnProperty(areaName))) {
            throw "storeConfiguration called for non-existent area " + areaName;
        }
        if (this.areas[areaName].items.hasOwnProperty(key)) {
            throw "Duplicate key for configuration " + areaName + ":" + key;
        }
        this.areas[areaName].items[key] = value;
    },

    getConfiguration: function (areaName, key) {
        if (!(this.areas.hasOwnProperty(areaName))) throw "getConfiguration called for non-existent area " + areaName;
        if (!(this.areas[areaName].items.hasOwnProperty(key))) {
            throw "getConfigurationByID: bad key " + areaName + ":" + key;
        }
        return this.areas[areaName].items[key];
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
            ConfigStore.emitChange()
            break;

        default:
        // no op
    }
});

module.exports = ConfigStore;
