var elixir = require('laravel-elixir');

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

// WARNING: at some point there may be a folder change:
//     https://laracasts.com/discuss/channels/general-discussion/important-elixir-folder-change

elixir(function(mix) {
    mix.sass('app.scss', 'public/css', {includePaths: [paths.bootstrap + 'stylesheets/']})
        .styles([
            'public/css/app.css',
            'resources/assets/css/local.css',
            './vendor/bower_components/FlexSlider/flexslider.css'
        ], './public/css/all.css', './')
        .browserify('app.js')
        .scripts([
            './vendor/bower_components/jquery/dist/jquery.js',
            './vendor/bower_components/jquery-ui/jquery-ui.min.js',
            './vendor/bower_components/bootstrap-sass-official/assets/javascripts/bootstrap.min.js',
            './vendor/bower_components/jquery-cookie/jquery.cookie.js',
            './vendor/bower_components/FlexSlider/jquery.flexslider-min.js'
        ], './public/js/all.js', './')
    ;
});

