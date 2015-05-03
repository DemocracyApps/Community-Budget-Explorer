var FluxDispatcher = require('flux').Dispatcher;

var dispatcher = new FluxDispatcher();
var dispatchQueue = [];

var inProcessing = false;

function queueAction(payload) {
    dispatchQueue.push(payload);
    if (!inProcessing) {
        startProcessing();
    }
}

function startProcessing() {
    inProcessing = true; // Editor claims this is 'never used', but that doesn't account for asynchronous calls

    while (dispatchQueue.length > 0) {
        if (dispatcher.isDispatching()) {
            return setTimeout(startProcessing, 2000); // Avoid an Invariant error from Flux
        }
        var payload = dispatchQueue.shift();
        dispatcher.dispatch(payload);
    }
    inProcessing = false;
}

var BudgetAppDispatcher = {
    isProcessing() {
        return inProcessing;
    },

    dispatch(payload) {
        queueAction(payload);
    },

    register(callback) {
        return dispatcher.register(callback);
    }
}

module.exports = BudgetAppDispatcher;
