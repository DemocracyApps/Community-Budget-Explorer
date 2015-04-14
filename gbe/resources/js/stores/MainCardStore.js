var dispatcher = require('../dispatcher/BudgetAppDispatcher');
var EventEmitter = require('events').EventEmitter;

var assign = require('object-assign');

var BudgetAppConstants = require('../constants/BudgetAppConstants');
var ActionTypes = BudgetAppConstants.ActionTypes;

var Card = require('../data/Card');
var CardSet = require('../data/CardSet');

var CHANGE_EVENT = 'change';

var MainCardStore = assign({}, EventEmitter.prototype, {

    idCounter: 0,

    versionCounter: 1, // Let's components optimize whether they need to redraw

    _cards: [],

    storeCard: function (data) {
        var card = new Card(this.versionCounter++, data.title, data.body, data.link, data.image);
        card.id = data.id;
        card.cardSet = data.cardSet;

        this._cards[this.idCounter] = card;
        this.emit(CHANGE_EVENT);
        return this.idCounter++;
    },
    
    storeCardSet: function (data) {
        var cardset = new CardSet(this.versionCounter++, data.name, data.cards);
        cardset.id= data.id;
        this._cards[this.idCounter] = cardset;
        this.emit(CHANGE_EVENT);
        return this.idCounter++;
    },

    dataHasUpdated: function (id, version) {
        if (id >= 0 && id < this._cards.length) {
            return (this._cards[id].version > version);
        }
        return false;
    },

    getData: function (id) {
        if (id >= 0 && id < this._cards.length) {
            return this._cards[id];
        }
        return null;
    },

    getDataIfUpdated: function (id, version) {
        if (this.dataHasUpdated(id,version)) {
            return this.getData(id);
        }
        return null;
    },

    getCardIfUpdated: function (id, version) {
        return this.getDataIfUpdated(id, version);
    },

    getCardSetIfUpdated: function (id, version) {
        return this.getDataIfUpdated(id, version);
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
            MainCardStore.emitChange()
            break;

        default:
        // no op
    }
});

module.exports = MainCardStore;
