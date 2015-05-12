beforeEach(function() {
  // need to use "_" suffix since 'let' is a token in ES6
  this.given = function(propName, getter) {
    var _lazy;

    Object.defineProperty(this, propName, {
      get: function() {
        if (!_lazy) {
          _lazy = getter.call(this);
        }

        return _lazy;
      },
      set: function() {},
      enumerable: true,
      configurable: true
    });
  };
});
