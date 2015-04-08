var SimpleCard = React.createClass({

    getInitialState: function() {
        return {
            card: null
        };
    },

    render: function() {
        console.log("Simplecard is rendering!");

        return <div key={this.props.key}> I am SimpleCard!</div>

        return (
            <div key={this.props.key}>
                <h1> {this.state.card.title} </h1>

                <p> {this.state.card.body} </p>
            </div>
        );
    }
});
