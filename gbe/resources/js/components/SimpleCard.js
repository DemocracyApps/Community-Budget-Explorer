import React from 'react';

var mainCardStore = require('../stores/MainCardStore');

var SimpleCard = React.createClass({

    getInitialState: function() {
        return {
            cardVersion: 0,
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
        var data = mainCardStore.getDataIfUpdated(this.props.data["mycard"].storeId, this.state.cardVersion);
        if (data != null) {
            console.log("SimpleCard is updating the data");
            this.setState({
                cardVersion: data.version,
                title: data.data.title,
                body: data.data.body,
                image: data.data.image,
                link: data.data.link
            });
        }
    },

    _onChange: function () {
        this.updateData();
    },

    render: function() {
        console.log("Simplecard is rendering with cardVersion " + this.state.cardVersion);
        if (this.state.cardVersion == 0) {
            return <div key={this.props.key}> I am SimpleCard!</div>
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
