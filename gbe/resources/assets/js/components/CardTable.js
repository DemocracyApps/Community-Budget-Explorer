import React from 'react';

var cardStore = require('../stores/CardStore');

var CardTable = React.createClass({

    propTypes: {
        site: React.PropTypes.object.isRequired,
        componentData: React.PropTypes.object.isRequired,
        componentProps: React.PropTypes.object.isRequired,
        storeId: React.PropTypes.number.isRequired
    },

    cardLink: function (card) {
        if (card.link) return (
            <div className="row">
                <div className="col-md-3">
                    <a style={{float:"right"}} href={card.link} target="_blank" className="btn-sm btn-info">Read More</a>
                </div>
                <div className="col-md-9"></div>
            </div>
        )
    },

    cardFunction: function(card, index) {
        if (card.body.length > 1) {
            return (
                <div key={index} className="col-md-6 card">
                    <div className="row">
                        <div className="col-md-12">
                            <h4>{card.title}</h4>
                        </div>
                        <div className="col-md-6">
                            <span dangerouslySetInnerHTML={{__html: card.body[0]}} />
                        </div>
                        <div className="col-md-6">
                            <span dangerouslySetInnerHTML={{__html: card.body[1]}} />
                        </div>
                        {this.cardLink(card)}
                    </div>
                </div>
            )
        }
        else if (card.image != null) {
            return (
                <div key={index} className="col-md-6 card">
                    <div className="row">
                        <div className="col-md-12">
                            <h4>{card.title}</h4>
                        </div>
                        <div className="col-md-4">
                            <a href={card.link} target="_blank" className="thumbnail">
                                <img src={card.image} alt={card.link}/>
                            </a>
                        </div>
                        <div className="col-md-8">
                            <span dangerouslySetInnerHTML={{__html: card.body[0]}} />
                        </div>
                        {this.cardLink(card)}
                    </div>
                </div>
            )
        }
        else {
            return (
                <div key={index} className="col-md-6 card">
                    <div className="row" style={{marginBottom:15}}>
                        <div className="col-md-12">
                            <h4>{card.title}</h4>
                            <span dangerouslySetInnerHTML={{__html: card.body[0]}} />
                        </div>
                        {this.cardLink(card)}
                    </div>
                </div>
            )
        }

    },

    render: function() {

        if (this.props.componentData["mycardset"] && this.props.componentData["mycardset"].ids.length > 0) {
            let colClasses = [
                "col-md-12",
                "col-md-12",
                "col-md-6 col-sm-12",
                "col-md-4 col-sm-12",
                "col-md-3 col-sm-6 col-xs-12"
            ];

            let maxColumns = Number(this.props.componentProps.maxColumns);
            let colClass = colClasses[maxColumns];
            let count = this.props.componentData["mycardset"].ids.length;
            let rowCount = Math.floor(count/maxColumns);
            let remainder = count%maxColumns;
            let hasRemainder = false;
            if (remainder > 0) {
                ++rowCount;
                hasRemainder = true;
            }

            let rows = [];
            let cardIndex = 0;
            for (let row=0; row<rowCount; ++row) {
                let cMax = (row == rowCount-1 && hasRemainder)?remainder:maxColumns;
                rows.push([]);
                for (let column=0; column<cMax; ++column) {
                    let card = cardStore.getCard(this.props.componentData["mycardset"].ids[cardIndex++]);
                    rows[row].push(card);
                }
            }

            var rowStyle = {
                marginTop: 1
            };
            var columnStyle = {
                background: "#eee",
                marginTop: 75,
                paddingLeft:50,
                paddingRight:5,
                paddingTop:5,
                paddingBottom:5
                //,
                //border: "1px solid black"
            };

// 3 cases: (a) 1 text section, (b) 2 text sections, (c) picture + 1 text section.
            var rowFunction = function (item, index) {
                return (
                    <div key={index} className="row card-table-row">
                        {item.map(this.cardFunction)}
                    </div>
                )
            };

            return (
                <div className="card-table">
                    {rows.map(rowFunction.bind(this))}
                </div>
            )
        }
    }
});

export default CardTable;
