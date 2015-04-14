import React from 'react';

var datasetStore = require('../stores/MainDataSetStore');

var SimpleCard = React.createClass({

    getInitialState: function() {
        return {
            datasets: [],
        };
    },

    allDataReady: function() {
        var ready = this.state.datasets.length > 0?true:false;
        for (var i=0; i<this.state.datasets.length && ready; ++i) {
            if (this.state.datasets[i].data == null) ready = false;
        }
        return ready;
    },

    componentWillMount: function () {
        /*
         * Let's request the data now.
         */
        var setList = this.props.data['alldata'].idList;
        for (var i=0; i<setList.length; ++i) {
            var dataset = {};
            dataset.storeId = setList[i];
            dataset.version = 0;
            dataset.data = null;
            datasetStore.getDataIfUpdated(setList[i], 0);
            this.state.datasets.push(dataset);
        }
    },

    componentDidMount: function () {
        this.updateData();
        datasetStore.addChangeListener(this._onChange);
    },

    componentWillUnmount: function () {
        datasetStore.removeChangeListener(this._onChange);
    },

    updateData: function () {
        for (var i=0; i<this.state.datasets.length; ++i) {
            var data = datasetStore.getDataIfUpdated(this.state.datasets[i].storeId, this.state.datasets[i].version);
            if (data != null) {
                var datasets = this.state.datasets;
                datasets[i].data = data;
                this.setState({datasets: datasets});
            }
        }
    },

    _onChange: function () {
        this.updateData();
    },

    render: function() {
        if (this.allDataReady()) {
            return (
                <div key={this.props.key}>
                    Got me some doggone data!
                </div>
            );
        }
        else {
            return <div key={this.props.key}> Multiyear table loading ...</div>
        }
    }
});

export default SimpleCard;
