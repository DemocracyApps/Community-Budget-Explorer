var Card = require('../data/Card');

function CardSet(version, name, cards) {
    this.class = 'CardSet';
    this.version = version;
    this.name = name;
    this.cards = [];
    for (var i=0; i<cards.length; ++i) {
        var card = new Card(null, cards[i].title, cards[i].body, cards[i].image, cards[i].link);
        this.cards.push(card);
    }
    this.getVersion = function() {
        return this.version;
    }
};



module.exports = CardSet;
