var dispatcher = require('../common/BudgetAppDispatcher');
var EventEmitter = require('events').EventEmitter;

var assign = require('object-assign');

var ActionTypes = require('../constants/ActionTypes');

var CHANGE_EVENT = 'change';

var StateStore = assign({}, EventEmitter.prototype, {

    currentId: 0,

    store: {
        global: {},
        components: {}
    },

    components: [],

    registerComponent: function registerComponent (parentId, name, initialState) {
        var id = this.currentId++;
        var component = {
            parentId: id,
            id: id,
            name: name,
            state: initialState
        }
        this.store.components[id] = component;
        return id;
    },

    unregisterComponent: function unregisterComponent (id) {
        delete this.store.components[id];
    },

    initializeGlobalState: function initializeGlobalState (section, values) {
        if (this.store.global[section] == undefined) this.store.global[section] = {};
        Object.assign(this.store.global[section], values);
    },

    initializeComponentState: function setComponentState (id, state) {
        if (this.store.components.hasOwnProperty(id)) {
            Object.assign(this.store.components[id].state, state);
        }
    },

    getGlobalValue: function getGlobalValue (section,key) {
        return this.store.global[section][key];
    },

    // For components
    getValue: function getValue (id, key) {
        var value = null;
        if (this.store.components[id].state.hasOwnProperty(key)) {
            value = this.store.components[id].state[key];
        }
        return value;
    },

    _setGlobalState: function setState (section, name, value) {
        if (this.store.global[section] == undefined) throw "Unknown global section " + section;
        if (this.store.global[section][name] === undefined) throw "Unknown global state variable " + name + " in section " + section;
        this.store.global[section][name] = value;
    },

    _setComponentState: function setComponentState (id, state) {
        if (this.store.components.hasOwnProperty(id)) {
            Object.assign(this.store.components[id].state, state);
        }
    },

    emitChange: function() {
        this.emit(CHANGE_EVENT);
    },

    addChangeListener: function(callback) {
        this.on(CHANGE_EVENT, callback);
    },

    removeChangeListener: function(callback) {
        this.removeListener(CHANGE_EVENT, callback);
    }
});

dispatcher.register(function (action) {
    switch (action.actionType)
    {
        case ActionTypes.INIT_CARD_STORE:
            StateStore.emitChange();
            break;

        case ActionTypes.STATE_CHANGE:
            {
                let changes = action.payload.changes;
                for (let i=0; i<changes.length; ++i) {
                    StateStore._setGlobalState (changes[i].section, changes[i].name, changes[i].value);
                }
                StateStore.emitChange();
            }
            break;

        case ActionTypes.COMPONENT_STATE_CHANGE:
            {
                let changes = action.payload.changes;
                var newState = {};
                for (let i=0; i<changes.length; ++i) {
                    newState[changes[i].name] = changes[i].value;
                }
                StateStore._setComponentState(action.payload.id, newState);
                StateStore.emitChange();
            }
            break;

        default:
        // no op
    }
});

module.exports = StateStore;
