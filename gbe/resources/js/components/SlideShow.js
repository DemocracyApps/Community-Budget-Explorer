import React from 'react';

var cardStore = require('../stores/CardStore');

var SlideShow = React.createClass({

    propTypes: {
        componentData: React.PropTypes.object.isRequired,
        stateId: React.PropTypes.number.isRequired
    },

    componentWillMount: function () {
    },

    componentDidMount: function () {
        $(this.getDOMNode()).flexslider();
    },

    componentDidUpdate: function () {
        $(this.getDOMNode()).flexslider();
    },

    render: function() {
        var cards = [];
        for (var i=0; i<this.props.componentData["mycardset"].ids.length; ++i) {
            var card = cardStore.getCard(this.props.componentData["mycardset"].ids[i]);
            if (card !== undefined) cards.push(card);
        }
        return (
            <div className="slider">
                <div className="flexslider">
                    <ul className="slides">
                        {cards.map(function (item, index) {
                            return (
                                <li key={index}>
                                    {item.title}
                                </li>
                            )
                        })}
                    </ul>
                </div>
            </div>
        );
    }
});

export default SlideShow;
