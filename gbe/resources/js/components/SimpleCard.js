import React from 'react';

var mainCardStore = require('../stores/MainCardStore');

var SimpleCard = React.createClass({

    propTypes: {
        data: React.PropTypes.object.isRequired,
    },

    getInitialState: function() {
        return {
            version: 0,
            title: null,
            body: null,
            image: null,
            link: null
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
        var card = mainCardStore.getCardIfUpdated(this.props.data['mycard'].id, this.state.version);
        console.log("In SimpleCard updateData: version = " + card.getVersion());
        if (card != null) {
            this.setState({
                version: card.getVersion(),
                title: card.title,
                body: card.body,
                image: card.image,
                link: card.link
            });
        }
    },

    _onChange: function () {
        this.updateData();
    },

    render: function() {
        if (this.state.version == 0) {
            return <div key={this.props.key}>SimpleCard loading ...</div>
        }
        else {
            return (
                <div key={this.props.key}>
                    <h1> {this.state.title} </h1>

                    <p> {this.state.body} </p>
                </div>
            );
        }
    }
});

export default SimpleCard;
