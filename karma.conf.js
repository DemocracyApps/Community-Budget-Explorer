module.exports = function (config) {
  config.set({
    browsers: [ 'Chrome' ], //run in Chrome
    singleRun: true, //just run once by default
    frameworks: [ 'browserify','jasmine' ], //use the mocha test framework
    files: [

      'resources/assets/js/spec_helper.js','resources/assets/js/*/__tests__/*.js'
    ],
    preprocessors: {
      'resources/assets/js/*/__tests__/*.js': [ 'browserify' ],
      'resources/assets/js/spec_helper.js': [ 'browserify' ]
    },
    browserify: {
      plugin: ['proxyquireify/plugin'],
      transform: [ 'babelify']
    },
    watchify:{
      poll: true
    },
    reporters: [ 'dots' ], //report results in this format
    singleRun: false,
    autoWatch: true
  });
};
