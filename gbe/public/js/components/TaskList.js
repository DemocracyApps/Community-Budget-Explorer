var TaskList = React.createClass({

   render: function() {

       var displayTask = function(task) {
           return <li>{task}</li>
       }

       return (
            <ul>
                {this.props.items.map(displayTask) }
            </ul>
       );
   }
});
