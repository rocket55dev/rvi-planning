const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Bootstrap Compilation (Separate Build)
 |--------------------------------------------------------------------------
 |
 | This config is for compiling Bootstrap separately from the main theme.
 | This improves build performance since Bootstrap doesn't need to be
 | recompiled on every theme change.
 |
 */

// Disable notifications
mix.disableNotifications();

// Set public path to current directory (theme root)
mix.setPublicPath('./');

// Configure sourcemaps
mix.sourceMaps(true, 'source-map');

// Bootstrap compilation
mix.sass('assets/sass/bootstrap-custom.scss', 'assets/css/bootstrap.css')
   .options({
      processCssUrls: false,
      sassOptions: {
         silenceDeprecations: ['legacy-js-api', 'import', 'global-builtin', 'color-functions'],
      }
   });

// Minify in production
if (mix.inProduction()) {
   mix.minify('assets/css/bootstrap.css');
}

// Webpack configuration
mix.webpackConfig({
   stats: {
      children: false,
      warnings: false,
   },
});
