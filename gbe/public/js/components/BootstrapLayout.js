var BootstrapLayout = React.createClass({

    getInitialState: function() {
        return {
            items: [
                'Item 1', 'Item 2'
            ],
            layout:null,
            counter:0,
            task:''
        };
    },

    onChange: function(e) {
        this.setState({task: e.target.value});
    },

    buildDiv: function (div) {
        return (<div id={div.id} key={div.id} className={div.class}> {div.id}</div>);
    },

    buildRow: function (row) {
        var cnt = this.state.counter++;
        alert("The count is " + cnt);
      return (
          <div key={"row_" + cnt} className="row">
              { row.divs.map(this.buildDiv) }
          </div>
      );
    },

    render: function() {
        console.log("The layout is " + JSON.stringify(this.state.layout));
        if (this.state.layout == null) {
            return <div>Placeholder</div>
        }
        return (
            <div>
                <h1> Page Title </h1>

                {this.state.layout.specification.rows.map(this.buildRow)}

            </div>
        );
    }
});





