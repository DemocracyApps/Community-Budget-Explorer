import React from 'react';

var TaskList = React.createClass({

   render: function() {

       var displayTask = function(task, index) {
           return <li key={index} >{task}</li>
       }

       return (
            <ul>
                {this.props.items.map(displayTask) }
            </ul>
       );
   }
});

export default TaskList;
