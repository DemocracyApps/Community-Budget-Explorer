import React from 'react';

var mainCardStore = require('../stores/MainCardStore');

var SlideShow = React.createClass({

    getInitialState: function() {
        return {
            version: 0,
            cards: []
        };
    },

    componentDidMount: function () {
        this.updateData();
        mainCardStore.addChangeListener(this._onChange);
    },

    componentWillUnmount: function () {
        mainCardStore.removeChangeListener(this._onChange);
    },

    updateData: function () {
        var data = mainCardStore.getDataIfUpdated(this.props.data["mycardset"].storeId, this.state.version);
        if (data != null) {
            console.log("SlideShow is updating the data");
            this.setState({
                version: data.version,
                cards: data.data.cards
            });
            console.log("Here are the cards: " + JSON.stringify(this.state.cards));
        }
    },

    _onChange: function () {
        this.updateData();
    },

    render: function() {
        console.log("SlideShow is rendering with version " + this.state.version);
        if (this.state.version == 0) {
            return <div key={this.props.key}> I am a SlideShow!</div>
        }
        else {
            return (
                <div key={this.props.key}>
                    <ul>
                        {this.state.cards.map(function (item, index) {
                            return <li key={index}> {item.title} </li>
                        })}
                    </ul>
                </div>
            );
        }
    }
});

export default SlideShow;
