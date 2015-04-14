import React from 'react';

var mainCardStore = require('../stores/MainCardStore');

var SlideShow = React.createClass({

    propTypes: {
        data: React.PropTypes.object.isRequired,
    },

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
        var cardset = mainCardStore.getCardSetIfUpdated(this.props.data["mycardset"].id, this.state.version);
        if (cardset != null) {
            this.setState({
                version: cardset.getVersion(),
                cards: cardset.cards
            });
        }
    },

    _onChange: function () {
        this.updateData();
    },

    render: function() {
        if (this.state.version == 0) {
            return <div key={this.props.key}>SlideShow loading ...</div>
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
