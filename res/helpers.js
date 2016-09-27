'use strict';

const fs = require('fs');
const gutil = require('gulp-util');

const browserSync = require('browser-sync');

const config = require(__dirname + '/asset');

class Helpers {

    /**
     * Class constructor
     *
     * @param  {Gulp} gulp GULP Object
     */
    constructor (gulp) {
        this._gulp = gulp;
        this._bSync = browserSync;

        // Load .env so we can share envvars while development
        const stats = fs.statSync('./.env');
        if (stats.isFile()) {
            require('dotenv').config();
        }

        // Require the package.json
        this.package = require(__dirname + '/../package');
    }

    /**
     * Determine is in local environment
     *
     * @return {boolean}
     */
    get isLocal () {
        return this.conf.url.includes('localhost');
    }

    /**
     * Get configurations
     *
     * @return {config}
     */
    get conf () {
        // Declaring 'serve' config
        config.port  = process.env.APP_PORT || config.serve.port; // 8088;
        config.host  = process.env.APP_HOST || config.serve.host; // 'localhost';
        config.url   = process.env.APP_URL  || config.serve.url;  // 'localhost:8088';
        config.proxy = config.serve.proxy;  // 'localhost:8000';

        if (!config.url || config.url.includes('localhost')) {
            config.url = config.host + ':' + config.port;
        }

        return config;
    }

    /**
     * Get webdriver.io config
     *
     * @return {Object}
     */
    get wdio () {
        let conf = {
            project: this.package.name,
            baseUrl: this.conf.url,
            host: 'hub.browserstack.com',
            user: process.env.BROWSERSTACK_USER,
            key: process.env.BROWSERSTACK_KEY,
            browserstackLocal: true,
            debug: true
        };

        // if (this.isLocal) {
        //     conf.baseUrl = process.env.BROWSERSTACK_URL
        // }

        return conf;
    }

    /**
     * Get enviroment development mode
     *
     * @return {String}
     */
    get mode () {
        // Determine build mode, default is 'dev'
        let mode = 'dev';

        // If mode is invalid, back to 'dev' mode
        if (['dev', 'prod'].indexOf(process.env.MODE) !== -1) {
            mode = process.env.MODE;
        }

        return mode;
    }

    /**
     * Initialize paths
     *
     * @return {Object}
     */
    get paths () {
        const deps = this.dependencies.join(',')
        const paths = {
            src: this.conf.paths.src,
            dest: this.conf.paths.dest
        };

        for (let key in this.conf.patterns) {
            paths[key] = [
                this.conf.paths.src + this.conf.patterns[key],
                'node_modules/' + deps + '**/*.{js,css,scss}'
            ];
        }

        return paths;
    }

    /**
     * Get list dependencies
     *
     * @return {Object}
     */
    get dependencies () {
        return Object.keys(this.package.dependencies)
    }

    /**
     * Get concated dependencies from 'package.json' file
     *
     * @return {Array}
     */
    get depsDir () {
        let deps = this.dependencies,
            dirs = [];

        for (let dep in deps) {
            dirs.push('node_modules/' + deps[dep]);
        }

        return dirs;
    }

    /**
     * Get server configuration
     *
     * @return {Object}
     */
    get server () {
        let server = this.conf.serve || {},
            config = {
                port: server.port || this.conf.port - 1,
                hostname: server.host || this.conf.host,
                base: server.base || './' + this.paths.dest.replace('/', '')
            };

        if ('proxy' in this.conf) {
            config.port = parseInt(this.conf.proxy.split(':').pop());
        }

        if ('bin' in server) {
            config.bin = server.bin;
        }

        if ('ini' in server) {
            config.ini = server.ini;
        }

        return config;
    }

    /**
     * Get browsersync callable instance
     *
     * @return {Function}
     */
    get sync () {
        return () => {
            this._bSync({
                port: this.conf.port,
                host: this.conf.host,
                proxy: this.conf.proxy,
                open: 'open' in this.conf.serve ? this.conf.serve.open : false,
                logConnections: false
            });
        }
    }

    /**
     * Simple helper to finalize each tasks
     *
     * @param  {Object}   stream Gulp pipe object
     * @param  {Function=} done  Gulp done function (Optional)
     * @return {Object}
     */
    build (stream, done) {
        const pipe = stream
            .pipe(this._gulp.dest(this.paths.dest))
            .pipe(this._bSync.stream());

        return done ? done() : pipe;
    }

    /**
     * Simple error handler
     *
     * @param  {Object} err Error instance
     */
    errorHandler (err) {
        helper.e(`[Error] ${err.stack}`, 'red');
    }

}

/**
 * @param  {Gulp} gulp
 * @return {Helpers}
 */
var helper = (gulp) => {
    return new Helpers(gulp);
};

helper.e = (message, color) => {
    color = color && color in gutil.colors ? color : 'green';

    const cb = gutil.colors[color];

    return gutil.log(cb(message));
};

module.exports = helper;
