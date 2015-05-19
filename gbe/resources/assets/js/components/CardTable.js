import React from 'react';

var cardStore = require('../stores/CardStore');

var CardTable = React.createClass({

    propTypes: {
        componentData: React.PropTypes.object.isRequired,
        componentProps: React.PropTypes.object.isRequired,
        storeId: React.PropTypes.number.isRequired
    },

    render: function() {

        console.log("Here we go: " + this.props.componentData["mycardset"].ids.length);

        if (this.props.componentData["mycardset"] && this.props.componentData["mycardset"].ids.length > 0) {
            let colClasses = [
                "col-md-12",
                "col-md-12",
                "col-md-6 col-sm-12",
                "col-md-4 col-sm-12",
                "col-md-3 col-sm-6 col-xs-12"
            ];

            let maxColumns = Number(this.props.componentProps.maxColumns);
            console.log("Max columns = " + maxColumns);
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

            var rowFunction = function (item, index) {
                return (
                    <div key={index} className="row" style={rowStyle}>
                        {item.map(function (colItem, colIndex) {
                            return (
                                <div className={colClass} style={{paddingLeft:5, paddingRight:5}}>
                                    <div key={colIndex} style={columnStyle}>
                                        <h2>{colItem.title}</h2>
                                        <br/>
                                        <span dangerouslySetInnerHTML={{__html: colItem.body}} />
                                    </div>
                                </div>
                            )
                        })}
                    </div>
                )
            };

            return (
                <div>
                    {rows.map(rowFunction)}
                </div>
            )
        }
    }
});

export default CardTable;
