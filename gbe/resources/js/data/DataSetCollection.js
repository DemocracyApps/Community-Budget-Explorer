var DatasetStatusConstants = require('../constants/DatasetStatusConstants');
var DatasetStatus = DatasetStatusConstants.DatasetStatus;

function DataSetCollection(version) {
    this.class = 'DataSetCollection';
    this.version = version;

    this.getVersion = function() {
        return this.version;
    }
};

module.exports = DataSetCollection;
