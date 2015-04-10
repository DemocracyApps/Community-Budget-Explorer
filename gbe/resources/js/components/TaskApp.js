import React from 'react';
import TaskList from './TaskList';

var TaskApp = React.createClass({

    getInitialState: function() {
        return {
            items: [
                'Item 1', 'Item 2'
            ],
            task:''
        };
    },

    addTask: function(e) {
        e.preventDefault();
        this.setState({
            items: this.state.items.concat(this.state.task),
            task: ''
        });

    },

    onChange: function(e) {
        this.setState({task: e.target.value});
    },

    render: function() {
        return (
          <div>
              <h1> My Tasks </h1>

              <TaskList items={this.state.items} />

              <form onSubmit={this.addTask}>
                  <input onChange={this.onChange} value={this.state.task}/>
                  <button>Add Task </button>
              </form>

          </div>
        );
    }
});

export default TaskApp;
