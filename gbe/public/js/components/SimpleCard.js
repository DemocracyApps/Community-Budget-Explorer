var BootstrapLayout = React.createClass({

    getInitialState: function() {
        return {
            card: null
        };
    },

    render: function() {
        if (this.state.card == null) {
            return <div>Placeholder</div>
        }
        return (
            <div>
                <h1> {this.state.card.title} </h1>

                <p> {this.state.card.body} </p>
            </div>
        );
    }
});
