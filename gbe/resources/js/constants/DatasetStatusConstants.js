var keyMirror = require('keymirror');

module.exports = {

    DatasetStatus: keyMirror({
        DS_STATE_NEW: null,
        DS_STATE_REQUESTED: null,
        DS_STATE_PENDING: null,
        DS_STATE_READY: null
    })

};
