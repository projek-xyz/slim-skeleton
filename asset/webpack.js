"use strict";

const path = require('path');
const webpack = require('webpack');

module.exports = (paths, configs) => {
    const webpackConfig = {
        output: {
            filename: '[name].js'
            path: path.resolve(paths.dest, 'scripts')
        }
    };

    return webpackConfig;
};
