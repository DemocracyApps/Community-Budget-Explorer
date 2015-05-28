var elixir = require('laravel-elixir');
require('babelify/polyfill');
/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

var paths = {
 'bootstrap': './vendor/bower_components/bootstrap-sass-official/assets/'
}
var browserifyOpts = {
    extensions: [ '.jsx', '.js' ]
};

elixir(function(mix) {
    mix.sass('app.scss', 'public/css', {includePaths: [paths.bootstrap + 'stylesheets/']})
        .styles([
            './vendor/bower_components/FlexSlider/flexslider.css',
            'public/css/app.css'
        ], './public/css/all.css', './')
        .browserify('app.js', null, null, browserifyOpts)
        .scripts([
            './vendor/bower_components/jquery/dist/jquery.js',
            './vendor/bower_components/jquery-ui/jquery-ui.min.js',
            './vendor/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap.min.js',
            './vendor/bower_components/jquery-cookie/jquery.cookie.js',
            './vendor/bower_components/FlexSlider/jquery.flexslider-min.js'
        ], './public/js/all.js', './')
        .copy('./vendor/bower_components/FlexSlider/fonts/flexslider-icon.ttf',
        './public/css/fonts/')
        .copy('./vendor/bower_components/FlexSlider/fonts/flexslider-icon.woff',
        './public/css/fonts/')
    ;
});


