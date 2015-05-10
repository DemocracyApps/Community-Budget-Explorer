module.exports = function (config) {
  config.set({
    browsers: [ 'Chrome' ], //run in Chrome
    singleRun: true, //just run once by default
    frameworks: [ 'browserify','jasmine' ], //use the mocha test framework
    files: [
      'resources/js/spec_helper.js','resources/js/*/__tests__/*.js'
    ],
    preprocessors: {
      'resources/js/*/__tests__/*.js': [ 'browserify' ],
      'resources/js/spec_helper.js': [ 'browserify' ]
    },
    browserify: {
      plugin: ['proxyquireify/plugin'],
      transform: [ 'babelify']
    },
    reporters: [ 'dots' ], //report results in this format
    singleRun: false,
    autoWatch: true
  });
};
