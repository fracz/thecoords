const {defineConfig} = require('@vue/cli-service')
const path = require("path");

const packageInfo = require('./package.json');
process.env.VUE_APP_VERSION = packageInfo.version;
process.env.VUE_APP_NAME = packageInfo.name;

module.exports = defineConfig({
    devServer: {
        proxy: {
            '/api': {target: 'http://[::1]:8082'},
        }
    },
    lintOnSave: false,
    outputDir: path.resolve(__dirname, '../public'),
    productionSourceMap: false,
    transpileDependencies: true,
})
