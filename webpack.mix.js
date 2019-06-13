const mix = require('laravel-mix');

// Public path helper
const publicPath = path => `${mix.config.publicPath}/${path}`;

// Source path helper
const src = path => `resources/assets/${path}`;

require('laravel-mix-export-tailwind-config');
require('laravel-mix-wp-blocks');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Sage application. By default, we are compiling the Sass file
 | for your application, as well as bundling up your JS files.
 |
 */

// Public Path
mix.setPublicPath('./dist');

// Browsersync
mix.browserSync({
  proxy: 'https://example.test',
  files: [
    '(app|config|resources)/**/*.php',
    publicPath`(styles|scripts)/**/*.(css|js)`,
  ],
});

// Styles
mix
  .postCss(src`styles/app.css`, 'styles')
  .postCss(src`styles/reset.css`, 'styles')
  .postCss(src`styles/admin.css`, 'styles')
  .postCss(src`styles/editor.css`, 'styles');

// JavaScript
mix.js(src`scripts/app.js`, 'scripts')
   .js(src`scripts/admin.js`, 'scripts')
   .block(src`scripts/editor.js`, 'scripts')
   .js(src`scripts/customizer.js`, 'scripts')
   .extract();

// Assets
mix.copyDirectory(src`images`, publicPath`images`)
   .copyDirectory(src`fonts`, publicPath`fonts`);

// Autoload
mix.autoload({
  jquery: ['$', 'window.jQuery'],
});

// Options
mix.options({
  processCssUrls: true,
  extractVueStyles: true,
  autoprefixer: false,
  postCss: [
    require('postcss-import'),
    require('postcss-inline-svg')({path: mix.config.publicPath}),
    require('tailwindcss')(src`styles/tailwind.js`),
    require('postcss-preset-env'),
    require('postcss-nested'),
    require('autoprefixer'),
  ],
});

mix.webpackConfig({
  externals: {
    'acf': 'acf',
  },
});

// Source maps when not in production.
if (!mix.inProduction()) {
  mix.sourceMaps();
}

// Hash and version files in production.
if (mix.inProduction()) {
  mix.version();
}

mix.exportTailwindConfig(src`styles/tailwind.js`, 'tailwind.json');
