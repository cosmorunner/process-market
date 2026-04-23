const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up as all the JS files.
 |
 */

mix.sass('resources/sass/app.scss', 'public/css').vue({ version: 2 });

mix.js('resources/js/app.js', 'public/js').vue({ version: 2 });
mix.js('resources/js/develop-app.js', 'public/js').vue({ version: 2 });
mix.js('resources/js/config-app.js', 'public/js').vue({ version: 2 });
mix.js('resources/js/utility-apps.js', 'public/js').vue({ version: 2 });
mix.js('resources/js/index.js', 'public/js').vue({ version: 2 });

mix.disableNotifications();

if (mix.inProduction()) {
    mix.version();
}

// Zusätzliche Optionen um den "hot" Modus auf Port 8888 laufen zu lassen, anstelle von 8080.
// Somit kann der "hot" Modus von der Allisa Plattform parallel laufen.
// Siehe auch "package.json" Portanpassung.
mix.options({
    hmrOptions: {
        host: 'localhost',
        port: '8888'
    },
})
