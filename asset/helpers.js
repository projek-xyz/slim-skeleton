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
    this.configs = this._loadConfig(__dirname + '/config');
  }

  /**
   * Initialize configuration
   *
   * @return {Object}
   */
  _loadConfig (configFile) {
    const configs = require(configFile);

    // Declaring 'serve' config
    configs.port = process.env.APP_PORT || configs.serve.port; // 8080;
    configs.host = process.env.APP_HOST || configs.serve.host; // 'localhost';
    configs.url  = process.env.APP_URL  || configs.serve.url;  // 'localhost:8000';

    return configs;
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
      src: this.configs.paths.src,
      dest: this.configs.paths.dest
    };

    for (let key in this.configs.patterns) {
      paths[key] = [
        this.configs.paths.src + this.configs.patterns[key],
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
   * Determine is in local environment
   *
   * @return {boolean}
   */
  get isLocal() {
    return this.configs.url.includes('localhost');
  }

  /**
   * Simple helper to finalize each tasks
   *
   * @param  {Object}   stream Gulp pipe object
   * @param  {Function} done   Gulp done function
   * @return {Object}
   */
  build (stream, done) {
    const pipe = stream
      .pipe(this._gulp.dest(this.paths.dest))
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
    helper.e(`[Error] ${err.stack}`, 'red');
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
