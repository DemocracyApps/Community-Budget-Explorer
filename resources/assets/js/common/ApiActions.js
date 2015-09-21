var configStore = require('../stores/ConfigStore');
var datasetStore = require('../stores/DatasetStore');
var dispatcher = require('../common/BudgetAppDispatcher');
var ActionTypes = require('../constants/ActionTypes');
var assign = require('object-assign');

var ApiActions = {

    requestDatasetIfNeeded: function requestDatasetIfNeeded (id) {
        var site = configStore.getConfiguration('common','site');
        var ds = datasetStore.getDataset(id);
        if (! ds.isReady() && ! ds.isRequested()) {
            console.log("Requesting dataset " + id + " via api");
            var source = site.apiUrl + "/datasets/" + id;
            $.get(source, function (r) {
            }).done(this.receiveData).fail(this.receiveError);
            ds.setRequested();
        }
    },

    receiveData: function receiveData (r) {
        for (var i=0; i<r.data.length; ++i) {
            dispatcher.dispatch({
                actionType: ActionTypes.DATASET_RECEIVED,
                payload: r.data[i]
            });
        }
    },

    receiveError: function receiveError(r) {
        console.log("ERROR - failed to get the data: " + JSON.stringify(r));
    }

};

module.exports = ApiActions;
