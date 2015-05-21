

var IdGenerator = {
    currentId: 109,

    generateId: function generateId() {
        return ++this.currentId;
    }
};

module.exports = IdGenerator;
