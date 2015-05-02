var keyMirror = require('keymirror');

module.exports = {

    ActionTypes: keyMirror({
        STATE_CHANGE: null,
        INIT_CARD_STORE: null,
        DATASET_RECEIVED: null,
        ALL_DATASETS_RECEIVED: null,
        CARDSET_READY: null,
        ALL_CARDSETS_READY: null,
        CLICK_THREAD: null,
        CREATE_MESSAGE: null,
        RECEIVE_RAW_MESSAGES: null
    })

};
