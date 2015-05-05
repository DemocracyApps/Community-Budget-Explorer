import React from 'react';

var cardStore = require('../stores/CardStore');

var SimpleCard = React.createClass({

    propTypes: {
        componentData: React.PropTypes.object.isRequired,
        stateId: React.PropTypes.number.isRequired
    },

    render: function() {
        var card = cardStore.getCard(this.props.componentData['mycard'].ids[0]);
        if (card == undefined) {
            return <div >SimpleCard loading ...</div>
        }
        else {
            return (
                <div>
                    <h1> {card.title} </h1>

                    <p> {card.body} </p>
                </div>
            );
        }
    }
});

export default SimpleCard;
