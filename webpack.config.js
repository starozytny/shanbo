var Encore = require('@symfony/webpack-encore');
var path   = require('path');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or subdirectory deploy
    //.setManifestKeyPrefix('build/')

    .addAliases({
        '@publicFolder': path.resolve(__dirname, './public'),
        '@dashboardComponents': path.resolve(__dirname, './assets/dashboard/js/components'),
        '@dashboardPages': path.resolve(__dirname, './assets/dashboard/js/pages'),
        '@commonComponents': path.resolve(__dirname, './assets/common/js/components'),
        '@appComponents': path.resolve(__dirname, './assets/app/js/components'),
        '@userComponents': path.resolve(__dirname, './assets/user/js/components'),
        '@userPages': path.resolve(__dirname, './assets/user/js/pages'),
        '@nodeModulesFolder': path.resolve(__dirname, './node_modules'),
    })

    .copyFiles({
        from: './assets/dashboard/fonts',
        to: 'dashboard/fonts/[path][name].[ext]',
    })
    .copyFiles({
        from: './assets/dashboard/images',
        to: 'dashboard/images/[path][name].[ext]',
    })

    .copyFiles({
        from: './assets/app/fonts',
        to: 'app/fonts/[path][name].[ext]',
    })
    .copyFiles({
        from: './assets/app/images',
        to: 'app/images/[path][name].[ext]',
    })

    .configureFilenames({
        css: !Encore.isProduction() ? 'css/[name].[fullhash:8].css' : 'css/[name].[fullhash:8].css',
        js: !Encore.isProduction() ? 'js/[name].[fullhash:8].js' : 'js/[name].[fullhash:8].js'
    })

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app/js/app.js')
    .addEntry('security', './assets/app/js/pages/security.js')
    .addEntry('contact', './assets/app/js/pages/contact.js')
    .addEntry('homepage', './assets/app/js/pages/homepage.js')
    .addEntry('photo', './assets/app/js/pages/photo.js')
    .addEntry('blog', './assets/app/js/pages/blog.js')

    .addEntry('dashboard', './assets/dashboard/js/app.js')
    .addEntry('dashboard_homepage', './assets/dashboard/js/pages/homepage.js')
    .addEntry('dashboard_styleguide', './assets/dashboard/js/pages/styleguide.js')
    .addEntry('dashboard_user', './assets/dashboard/js/pages/user.js')
    .addEntry('dashboard_settings', './assets/dashboard/js/pages/settings.js')
    .addEntry('dashboard_contact', './assets/dashboard/js/pages/contact.js')
    .addEntry('dashboard_notifications', './assets/dashboard/js/pages/notifications.js')
    .addEntry('dashboard_changelog', './assets/dashboard/js/pages/changelog.js')

    .addEntry('user', './assets/user/js/app.js')
    .addEntry('user_homepage', './assets/user/js/pages/homepage.js')
    .addEntry('user_profil', './assets/user/js/pages/profil.js')
    .addEntry('user_albums', './assets/user/js/pages/albums.js')
    .addEntry('user_photos', './assets/user/js/pages/photos.js')

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    //.enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // configure Babel
    // .configureBabel((config) => {
    //     config.plugins.push('@babel/a-babel-plugin');
    // })

    // enables and configure @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    .enablePostCssLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    .enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
