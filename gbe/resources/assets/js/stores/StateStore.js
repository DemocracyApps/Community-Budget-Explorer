var dispatcher = require('../common/BudgetAppDispatcher');
var EventEmitter = require('events').EventEmitter;

var assign = require('object-assign');

var ActionTypes = require('../constants/ActionTypes');

var CHANGE_EVENT = 'change';

/*
 * Ok, we have a couple kinds of state, at least:
 *  - Internal component state - stuff only the component will ever care about.
 *  - Shared state - things (like current page) that at least 2 components need to know
 *
 *  So at least we definitely need to introduce areas. A couple obvious ones are:
 *   - site: things about the state of the whole site. Page is one. View styles might be another.
 *   - components:
 */

var StateStore = assign({}, EventEmitter.prototype, {

    currentId: 0,

    store: {
        site: {},
        components: {}
    },

    components: [],

    registerComponent: function registerComponent (type, name, initialState) {
        var id = this.currentId++;
        var component = {
            id: id,
            type: type,
            name: name,
            state: initialState
        }
        this.store.components[id] = component;
        return id;
    },

    registerState: function registerState (path, value) {
        var pathArray = path.split(".");
        var current = this.store;
        while (pathArray.length > 1) {
            current = current[pathArray.shift()];
            if (current === undefined) throw "Undefined state path " + path;
        }
        current[pathArray.shift()] = value;
    },

    setState: function setState (path, value) {
        var pathArray = path.split(".");
        var current = this.store;
        while (pathArray.length > 1) {
            current = current[pathArray.shift()];
            if (current === undefined) throw "Undefined state path " + path;
        }
        var stateVariable= pathArray.shift();
        if (current[stateVariable] === undefined) throw "Unknown state variable " + stateVariable + " in path " + path;
        current[stateVariable] = value;
    },

    getValue: function getValue (/*path OR id, key */) {
        if (arguments.length == 1)
            return this.getStateValue(arguments[0]);
        else
            return this.getComponentStateValue(arguments[0], arguments[1]);
    },

    getStateValue: function getStateValue (path) {
        var pathArray = path.split(".");
        var value = this.store;
        while (pathArray.length > 0) {
            value = value[pathArray.shift()];
        }
        return value;
    },

    setComponentState: function setComponentState (id, state) {
        if (this.store.components.hasOwnProperty(id)) {
            Object.assign(this.store.components[id].state, state);
        }
    },

    getComponentState: function getComponentState(id) {
        var state = {};
        if (this.store.components.hasOwnProperty(id)) {
            state = this.store.components[id].state;
        }
        return state;
    },

    getComponentStateValue: function getComponentStateValue(id, key) {
        var value = null;
        if (this.store.components[id].state.hasOwnProperty(key)) {
            value = this.store.components[id].state[key];
        }
        return value;
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
            StateStore.emitChange();
            break;

        case ActionTypes.STATE_CHANGE:
            {
                let changes = action.payload.changes;
                for (let i=0; i<changes.length; ++i) {
                    StateStore.setState (changes[i].name, changes[i].value);
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
                StateStore.setComponentState(action.payload.id, newState);
                StateStore.emitChange();
            }
            break;

        default:
        // no op
    }
});

module.exports = StateStore;
