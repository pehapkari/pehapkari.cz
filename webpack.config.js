var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .autoProvidejQuery()
    .autoProvideVariables({
        "window.Bloodhound": require.resolve('bloodhound-js'),
        "jQuery.tagsinput": "bootstrap-tagsinput"
    })
    .enableSassLoader()
    .enableVersioning()
    .addStyleEntry('css/app', ['./assets/scss/app.scss'])
    .addStyleEntry('css/admin', ['./assets/scss/admin.scss'])
;

module.exports = Encore.getWebpackConfig();
