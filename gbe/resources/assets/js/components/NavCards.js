import React from 'react';

var cardStore = require('../stores/CardStore');
var configStore = require('../stores/ConfigStore');
var dispatcher = require('../common/BudgetAppDispatcher');
var ActionTypes = require('../constants/ActionTypes');

var NavCards = React.createClass({

    propTypes: {
        site: React.PropTypes.object.isRequired,
        componentData: React.PropTypes.object.isRequired,
        componentProps: React.PropTypes.object.isRequired,
        storeId: React.PropTypes.number.isRequired
    },


    render: function() {
        var cards = [];
        for (var i=0; i<this.props.componentData["mycardset"].ids.length; ++i) {
            var card = cardStore.getCard(this.props.componentData["mycardset"].ids[i]);
            if (card !== undefined) cards.push(card);
        }
        if (cards.length != 3) throw "NavCards currently only set for 3 cards";
        var colors = ["#7DA8CC","#A4CC56","#41A7BF","#A58A6A","#E56B41","#856AC6","#F08B27"];

        var gotoPage = function(pageName) {
            console.log("Go to page " + pageName);
            var page = configStore.getConfiguration('pagesByShortName', pageName);
            dispatcher.dispatch({
                actionType: ActionTypes.STATE_CHANGE,
                payload: {
                    changes: [
                        {
                            name: "site.currentPage",
                            value: page.id
                        }
                    ]
                }
            });
        };

        if (card == undefined) {
            return <div >NavCards loading ... </div>
        }
        else {
            return (
                <div className="row">
                    {cards.map(function(card, index) {
                        var cardStyle = {
                            color: "white !important",
                            display: "block",
                            padding: 30,
                            position: "relative",
                            background: colors[index],
                            height: 200
                        };
                        return (
                            <a href="#" onClick={gotoPage.bind(null,card.link)}>
                            <div className="col-xs-4" style={cardStyle}>
                                <h1> {card.title} </h1>

                                <span dangerouslySetInnerHTML={{__html: card.body[0]}}/>
                            </div>
                            </a>
                        )
                    })}
                </div>
            );
        }
    }
});

export default NavCards;
