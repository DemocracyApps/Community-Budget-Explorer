var keyMirror = require('keymirror');

/*
 dataCommands: [
 {
 command: 'selectAccountTypes',
 values: [AccountTypes.EXPENSE, AccountTypes.REVENUE]
 },
 {
 command: 'select', // Same as above
 conditions: [
 {
 field: 'accountType',
 comparison: '=',
 values: [AccountTypes.EXPENSE, AccountTypes.REVENUE]
 }
 ]
 },
 {
 command: 'setAmountThreshold',
 value: 0.01,
 abs: true
 },
 {
 command: 'discard', // Same as above
 conditions: [
 {
 field: 'amount',
 transform: Math.abs,
 comparison: '<',
 value: 0.01
 }
 ]
 },
 {
 command: 'setHierarchy', // Primary immediate effect is to aggregate up all other hierarchy levels
 fields: [
 'Fund',
 'Department',
 'Division'
 ]
 }
 ]

 */
module.exports = {

    PipelineCommands: keyMirror({
        select: null,
        discard: null,
        selectAccounts: null,
        setAmountThreshold: null,
        setHierarchy: null,
        toArray: null,
        toTree: null
    })

};
