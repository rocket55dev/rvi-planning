const mix = require('laravel-mix');
const fs = require('fs');
const path = require('path');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

// Disable notifications
mix.disableNotifications();

// Disable Mix's built-in success messages
mix.disableSuccessNotifications();

// Set public path to current directory (theme root)
mix.setPublicPath('./');

// Configure sourcemaps for all environments
mix.sourceMaps(true, 'source-map');

// Main theme stylesheet
mix.sass('assets/sass/style.scss', 'style.css')
   .options({
      processCssUrls: false,
      postCss: [],
   });

// Gravity Forms overrides (separate stylesheet)
mix.sass('assets/sass/gravity-forms.scss', 'assets/css/gravity-forms.css')
   .options({
      processCssUrls: false,
      postCss: [],
   });

// Main JavaScript - concatenate all files from concat folder
const concatDir = path.resolve(__dirname, 'assets/scripts/concat');
if (fs.existsSync(concatDir)) {
   const concatFiles = fs.readdirSync(concatDir)
      .filter(file => file.endsWith('.js'))
      .sort()
      .map(file => `assets/scripts/concat/${file}`);

   if (concatFiles.length > 0) {
      mix.scripts(concatFiles, 'assets/scripts/main.min.js');
   }
}

// Compile JS files in assets/js folder
const assetsJsDir = path.resolve(__dirname, 'assets/js');
if (fs.existsSync(assetsJsDir)) {
   const jsFiles = fs.readdirSync(assetsJsDir)
      .filter(file => file.endsWith('.js') && !file.endsWith('.min.js'));

   jsFiles.forEach(file => {
      const baseName = file.replace('.js', '');
      mix.js(
         `assets/js/${file}`,
         `assets/js/${baseName}.min.js`
      ).options({
         terser: { extractComments: false }
      });
   });
}

// BrowserSync
mix.browserSync({
   proxy: 'https://rviplanning.local',
   files: [
      '**/*.css',
      '**/*.js',
      '**/*.php'
   ],
   notify: false,
   open: true,
   https: true,
});

// Dynamically compile all block SCSS and JS files
const blocksDir = path.resolve(__dirname, 'blocks');

if (fs.existsSync(blocksDir)) {
   const blockDirs = fs.readdirSync(blocksDir, { withFileTypes: true })
      .filter(dirent => dirent.isDirectory())
      .map(dirent => dirent.name);

   blockDirs.forEach(blockName => {
      const scssPath = path.resolve(blocksDir, blockName, `sass/${blockName}.scss`);
      if (fs.existsSync(scssPath)) {
         mix.sass(
            `blocks/${blockName}/sass/${blockName}.scss`,
            `blocks/${blockName}/style.css`
         ).options({
            processCssUrls: false,
            postCss: [],
         });
      }

      // Block JS
      const jsPath = path.resolve(blocksDir, blockName, `js/${blockName}.js`);
      console.log('Checking JS:', jsPath);

      if (fs.existsSync(jsPath)) {
         mix.js(
            `blocks/${blockName}/js/${blockName}.js`,
            `blocks/${blockName}/js/${blockName}.min.js`
         ).options({
            terser: { extractComments: false }
         });
      }
   });
}


// Version files for cache busting (optional - only if you want hashed filenames)
// mix.version();

// Don't minify in dev mode for faster builds
if (mix.inProduction()) {
   mix.minify('style.css')
      .minify('assets/scripts/main.min.js');

   // Minify all block files
   const blockDirs = fs.readdirSync(blocksDir, { withFileTypes: true })
      .filter(dirent => dirent.isDirectory())
      .map(dirent => dirent.name);

   blockDirs.forEach(blockName => {
      const cssPath = path.resolve(blocksDir, blockName, 'style.css');
      if (fs.existsSync(cssPath)) {
         mix.minify(`blocks/${blockName}/style.css`);
      }

      const jsPath = path.resolve(blocksDir, blockName, 'js/scripts.min.js');
      if (fs.existsSync(jsPath)) {
         mix.minify(`blocks/${blockName}/js/scripts.min.js`);
      }
   });
}

// Webpack configuration
mix.webpackConfig({
   module: {
      rules: [
         {
            test: /\.scss$/,
            use: [
               {
                  loader: 'sass-loader',
                  options: {
                     sourceMap: true,
                     additionalData: `
                        @import './assets/sass/utilities/functions/index';
                        @import './assets/sass/utilities/variables/index';
                        @import './assets/sass/utilities/mixins/index';
                     `,
                     sassOptions: {
                        silenceDeprecations: ['legacy-js-api', 'import', 'global-builtin', 'color-functions'],
                     },
                  },
               },
            ],
         },
      ],
   },
   stats: {
      children: false,
      warnings: false,
   },
});