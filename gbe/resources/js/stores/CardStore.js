var dispatcher = require('../common/BudgetAppDispatcher');
var EventEmitter = require('events').EventEmitter;

var assign = require('object-assign');

var ActionTypes = require('../constants/ActionTypes');

var Card = require('../data/Card');

var CHANGE_EVENT = 'change';

var CardStore = assign({}, EventEmitter.prototype, {

    timestamp: 0,
    _cards: [],

    storeCard: function (data) {
        var card = new Card(this.timestamp++, data.title, data.body, data.link, data.image);
        card.id = data.id;
        card.cardSet = data.cardSet;
        this._cards[card.id] = card;
        this.emit(CHANGE_EVENT);
    },

    getCard: function (id) {
        return this._cards[id];
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

module.exports = CardStore;
