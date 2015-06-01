import React from 'react';

var cardStore = require('../stores/CardStore');

var SimpleCard = React.createClass({

    propTypes: {
        site: React.PropTypes.object.isRequired,
        componentData: React.PropTypes.object.isRequired,
        componentProps: React.PropTypes.object.isRequired,
        storeId: React.PropTypes.number.isRequired
    },


    render: function() {
        var card = cardStore.getCard(this.props.componentData['mycard'].ids[0]);

        var renderTitle = function(tagLevel, card) {
            if (tagLevel && tagLevel > 0) {
                switch (tagLevel) {
                    case 1:
                        return <h1>{card.title}</h1>
                        break;
                    case 2:
                        return <h2>{card.title}</h2>
                        break;
                    case 3:
                        return <h3>{card.title}</h3>
                        break;
                    case 4:
                        return <h4>{card.title}</h4>
                        break;
                    case 5:
                        return <h5>{card.title}</h5>
                        break;
                }
            }
        };

        if (card == undefined) {
            return <div >SimpleCard loading ... </div>
        }
        else {
            return (
                <div className="row">
                    <div className="col-md-12">
                        {renderTitle(Number(this.props.componentProps.headerTag), card)}

                        <span dangerouslySetInnerHTML={{__html: card.body}} />
                    </div>
                </div>
            );
        }
    }
});

export default SimpleCard;
