var DatasetStatusConstants = require('../constants/DatasetStatusConstants');
var DatasetStatus = DatasetStatusConstants.DatasetStatus;

function DataSet(version, datasetId) {
    this.class = 'DataSet';
    this.version = version;
    this.datasetId = datasetId;
    this.status = DatasetStatus.DS_STATE_NEW;
    this.data = null;

    this.getStatus = function() {
        return this.status;
    };

    this.isReady = function() {
        return (this.status == DatasetStatus.DS_STATE_READY);
    };

    this.setReady = function() {
        this.status = DatasetStatus.DS_STATE_READY;
    }

    this.isRequested = function() {
        return (this.status == DatasetStatus.DS_STATE_REQUESTED);
    };

    this.setRequested = function() {
        this.status = DatasetStatus.DS_STATE_REQUESTED;
    };

    this.getVersion = function() {
        return this.version;
    }
};

module.exports = DataSet;
