import React from 'react';

var cardStore = require('../stores/CardStore');

var SimpleCard = React.createClass({

    propTypes: {
        componentData: React.PropTypes.object.isRequired,
        componentProps: React.PropTypes.object.isRequired,
        storeId: React.PropTypes.number.isRequired
    },


    render: function() {
        var card = cardStore.getCard(this.props.componentData['mycard'].ids[0]);

        var cardStyle = {
            color: "white !important",
            display:"block",
            padding: "30px 0px",
            position:"relative",
            background:"#7DA8CC",
            height: 300
        };

        if (card == undefined) {
            return <div >SimpleCard loading ... </div>
        }
        else {
            return (
                <div style={cardStyle}>
                    <h1> {card.title} </h1>

                    <span dangerouslySetInnerHTML={{__html: card.body[0]}} />
                </div>
            );
        }
    }
});

export default SimpleCard;
