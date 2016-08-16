'use strict';

const fs = require('fs');
const gutil = require('gulp-util');

class Helpers {

    /**
     * Class constructor
     *
     * @param  {Object} gulp GULP Object
     * @param  {Object} sync BrowserSync Object
     */
    constructor (gulp, sync) {
        this._gulp = gulp;
        this._sync = sync;

        // Load .env so we can share envvars while development
        const stats = fs.statSync('./.env');
        if (stats.isFile()) {
            require('dotenv').config();
        }

        // Require the package.json
        this.package = require(__dirname + '/../package');

        // Setup configurations
        this.configs = this._getConfigs(__dirname + '/config');

        // Setup paths
        this.paths = this._getPaths();

        // Simply determine whether this build is for production use?
        this.production = this.configs.mode !== 'dev';
    }

    /**
     * Initialize configuration
     *
     * @return {Object}
     */
    _getConfigs (configFile) {
        const configs = require(configFile);

        configs.mode = this._getMode(process.env.MODE);

        // Declaring 'serve' config
        configs.port = process.env.APP_PORT || configs.serve.port; // 8080;
        configs.host = process.env.APP_HOST || configs.serve.host; // 'localhost';
        configs.url  = process.env.APP_URL  || configs.serve.url;  // 'localhost:8000';

        return configs;
    }

    /**
     * Get enviroment development mode
     *
     * @param  {String} envMode Mode from system envvars
     * @return {String}
     */
    _getMode (envMode) {
        // Determine build mode, default is 'dev'
        let mode = 'dev';

        // If mode is invalid, back to 'dev' mode
        if (['dev', 'prod'].indexOf(envMode) !== -1) {
            mode = envMode;
        }

        return mode;
    }

    /**
     * Initialize paths
     *
     * @return {Object}
     */
    _getPaths () {
        const paths = {
            src: this.configs.paths.src,
            dest: this.configs.paths.dest
        };

        for (let key in this.configs.patterns) {
            paths[key] = [
                this.configs.paths.src + this.configs.patterns[key],
                this._getDepsDir() + '**/*.{js,css,scss}'
            ];
        }

        return paths;
    }

    /**
     * Get concated dependencies from 'package.json' file
     *
     * @return {String}
     */
    _getDepsDir () {
        return 'node_modules/{' + Object.keys(this.package.dependencies).join(',') + '}/';
    }

    /**
     * Simple helper to finalize each tasks
     *
     * @param  {Object}   stream Gulp pipe object
     * @param  {Function} done   Gulp done function
     * @return {Object}
     */
    build (stream, done) {
        const pipe = stream.pipe(this._gulp.dest(this.paths.dest))
            .pipe(this._sync.stream());

        if (done) {
            return done();
        }

        return pipe;
    }

    /**
     * Simple error handler
     *
     * @param  {Object} err Error instance
     * @return {Mixed}
     */
    errorHandler (err) {
        const message = err.message.replace(err.fileName + ': ', '');
        let filename = err.fileName.replace(__dirname, '');

        if (err.lineNumber) {
            filename += ` (${err.lineNumber})`;
        }

        helper.e([
            `[Error]`,
            `  message: ${message}`,
            ` filename: ${filename}`
        ].join('\n'), 'red');
    }
}

var helper = (gulp, sync) => {
  return new Helpers(gulp, sync);
};

helper.e = (message, color) => {
  color = color && color in gutil.colors ? color : 'green';

  const cb = gutil.colors[color];

  return gutil.log(cb(message));
};

module.exports = helper;
