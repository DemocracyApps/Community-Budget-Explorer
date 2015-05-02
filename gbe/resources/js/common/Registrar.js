
var Registrar = assign({}, {
    currentCounter: 0,

    getUniqueKey: function getUniqueKey () {
        return this.currentCounter++;
    }

});

module.exports = Registrar;

