var DatasetStatusConstants = require('../constants/DatasetStatusConstants');
var DatasetStatus = DatasetStatusConstants.DatasetStatus;

function Dataset(version, localId, serverId) {
    this.version = version;
    this.localId = localId;
    this.serverId = serverId;
    this.status = DatasetStatus.DS_STATE_NEW;
    this.data = null;

    this.receiveDataset = function (data, newVersion) {
        this.version = newVersion;
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

    this.getVersion = function() {
        return this.version;
    };
};

module.exports = Dataset;
