var DatasetStatus = require('../constants/DatasetStatus');

function Dataset(timestamp, sourceId) {
    this.timestamp = timestamp;
    this.sourceId = sourceId;
    this.status = DatasetStatus.DS_STATE_NEW;
    this.data = null;

    this.receiveDataset = function (data, newTimestamp) {
        this.timestamp = newTimestamp;
        this.status = DatasetStatus.DS_STATE_READY;
        this.data = data;
    };

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

    this.getTimestamp = function() {
        return this.timestamp;
    };
};

module.exports = Dataset;
