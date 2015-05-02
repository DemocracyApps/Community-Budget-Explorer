import React from 'react';

var cardStore = require('../stores/CardStore');

var SimpleCard = React.createClass({

    propTypes: {
        data: React.PropTypes.object.isRequired,
    },

    componentDidMount: function () {
        cardStore.addChangeListener(this._onChange);
    },

    componentWillUnmount: function () {
        cardStore.removeChangeListener(this._onChange);
    },


    _onChange: function () {
        // Nothing, actually
    },

    render: function() {
        console.log("Simple card rendering");
        var card = cardStore.getCard(this.props.data['mycard'].ids[0]);
        if (card == undefined) {
            return <div key={this.props.key}>SimpleCard loading ...</div>
        }
        else {
            return (
                <div key={this.props.key}>
                    <h1> {card.title} </h1>

                    <p> {card.body} </p>
                </div>
            );
        }
    }
});

export default SimpleCard;
