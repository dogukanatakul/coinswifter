const mix = require('laravel-mix');


mix.webpackConfig({
    // other config here
    output: {
        chunkFilename: 'assets/js/[name].js?id=[chunkhash]',
    }
});

mix.js('resources/js/main.js', 'public/assets/js')
    .vue()
    .sass('resources/js/assets/scss/app.scss', 'public/assets/css').options({
    processCssUrls: false
})
    .copyDirectory('resources/js/assets/img', 'public/assets/img')
    .copyDirectory('resources/js/assets/js', 'public/assets/js');


