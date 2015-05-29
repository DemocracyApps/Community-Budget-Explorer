import React from 'react';

var cardStore = require('../stores/CardStore');

var SlideShow = React.createClass({

    propTypes: {
        site: React.PropTypes.object.isRequired,
        componentData: React.PropTypes.object.isRequired,
        componentProps: React.PropTypes.object.isRequired,
        storeId: React.PropTypes.number.isRequired
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
        var overlayStyle = {
            zIndex:"100",
            position:"absolute",
            width:"50%",
            top:110,
            right:"10%",
            background: "#666",
            padding: "20px 30px 30px 30px",
            color:"white"
        };

        var imgStyle={
            zIndex:"1"
        };

        var showLink = function(card) {
            if (card.link) {
                return (
                    <div> <a href={card.link} target="_blank" className="btn-sm btn-info">Read More</a></div>
                )
            }
        };

        return (
            <div className="slider" >
                <div className="flexslider">
                    <ul className="slides">
                        {cards.map(function (item, index) {
                            return (
                                <li key={index}>
                                    <img src={item.image} style={imgStyle}/>
                                    <div style={overlayStyle}>
                                        <h2>{item.title}</h2>
                                        <span dangerouslySetInnerHTML={{__html: item.body[0]}} />
                                        {showLink(item)}
                                    </div>
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
