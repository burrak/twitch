const Encore = require('@symfony/webpack-encore')

if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev')
}

Encore
.setOutputPath('public/build')
.setPublicPath('/build')
.addEntry('app', './assets/app.js')
.addEntry('files', './assets/js/files/Index.ts')
.enableSassLoader()
.enableTypeScriptLoader()
.enableSingleRuntimeChunk()
.enableBuildNotifications()
.enableVersioning(Encore.isProduction())
.enableSourceMaps(!Encore.isProduction())
.cleanupOutputBeforeBuild()
.splitEntryChunks()
.configureBabelPresetEnv((config) => {
config.useBuiltIns = 'usage'
config.corejs = 3
})


module.exports = Encore.getWebpackConfig()
