/** jslint node: true */
'use strict';

const fs = require('fs');
const path = require('path');
const gutil = require('gulp-util');

const browserSync = require('browser-sync');

const config = require(__dirname + '/config');

class Helpers {

    /**
     * Class constructor
     *
     * @param  {Gulp} gulp GULP Object
     */
    constructor (gulp) {
        this._gulp = gulp;
        this.bSync = browserSync;

        try {
            // Load .env so we can share envvars while development
            const stats = fs.statSync('./.env');
            if (stats.isFile()) {
                require('dotenv').config();
            }
        } catch (e) {
            // Do nothing
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
        return this.conf.url.includes('localhost') || this.mode == 'local';
    }

    /**
     * Get configurations
     *
     * @return {config}
     */
    get conf () {
        // Declaring 'serve' config
        config.port = process.env.APP_PORT || config.serve.port; // 8088;
        config.host = process.env.APP_HOST || config.serve.host; // 'localhost';
        config.url = process.env.APP_URL  || config.serve.url;  // 'localhost:8088';
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
        let wdioConf = {
            project: this.package.name,
            baseUrl: 'http://' + this.conf.url,
            debug: true
        };

        // Check if it has BROWSERSTACK_* in envvar
        if ('BROWSERSTACK_USER' in process.env && 'BROWSERSTACK_KEY' in process.env) {
            wdioConf.host = 'hub.browserstack.com';
            wdioConf.user = process.env.BROWSERSTACK_USER;
            wdioConf.key = process.env.BROWSERSTACK_KEY;
            wdioConf.services = ['browserstack'];

            if (this.isLocal) {
                // process.env.BROWSERSTACK_URL
                wdioConf.browserstackLocal = true;
            }
        }

        return wdioConf;
    }

    /**
     * Get enviroment development mode
     *
     * @return {String}
     */
    get mode () {
        // Determine build mode, default is 'local'
        let mode = 'local',
            modes = ['development', 'production', 'testing', 'local'];

        // If mode is invalid, back to 'dev' mode
        if (modes.indexOf(process.env.APP_ENV) > -1) {
            mode = process.env.APP_ENV;
        }

        if ('CI' in process.env) {
            mode = 'testing';
        }

        return mode;
    }

    /**
     * Initialize paths
     *
     * @return {Object}
     */
    get paths () {
        const paths = {
            src: this.conf.paths.src,
            dest: this.conf.paths.dest
        };

        for (let key in this.conf.patterns) {
            paths[key] = [
                paths.src + this.conf.patterns[key]
            ];
        }

        paths.vendor = path.dirname(paths.dest) + '/vendor';

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
     * Get vendors directory patterns
     *
     * @return {array}
     */
    get vendors () {
        let vendors = [];

        for (let i in this.depsPath) {
            let name = this.depsPath[i].split('/').pop();

            if (['bootstrap', 'jquery'].indexOf(name) > -1) {
                vendors.push(this.depsPath[i] + '/dist/**/*.{js,css}');
            } else if (name == 'font-awesome') {
                vendors.push(this.depsPath[i] + '/{fonts,css}/*.{css,eot,svg,ttf,woff,woff2}');
            } else {
                vendors.push(this.depsPath[i] + '/**/*.js');
            }

            vendors.push('!' + this.depsPath[i] + '/**/*min.{js,css}');
        }

        return vendors;
    }

    /**
     * Get concated dependencies from 'package.json' file
     *
     * @return {Array}
     */
    get depsPath () {
        let dirs = [];

        for (let dep in this.dependencies) {
            dirs.push('node_modules/' + this.dependencies[dep]);
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

        for (let key in ['bin', 'ini']) {
            if (key in server) {
                config[key] = server[key];
            }
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
            let bSyncConfig = {
                port: this.conf.port,
                host: this.conf.host,
                proxy: this.conf.proxy,
                open: 'open' in this.conf.serve ? this.conf.serve.open : false,
                logConnections: false
            };

            this.bSync(bSyncConfig);
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
            .pipe(this.bSync.stream());

        return done ? done() : pipe;
    }

    /**
     * Simple error handler
     *
     * @param  {Object} err Error instance
     */
    logErrors (err) {
        gutil.log(gutil.colors.red(`[Error] ${err.stack}`));
    }

    log (message, color) {
        color = color && color in gutil.colors ? color : 'green';

        const cb = gutil.colors[color];

        return gutil.log(cb(message));
    }

}

/**
 * @param  {Gulp} gulp
 * @return {Helpers}
 */
module.exports = (gulp) => {
    return new Helpers(gulp);
};
