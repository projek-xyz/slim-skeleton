'use strict';

/* Gulp set up
 --------------------------------------------------------------------------------- */

const gulp = require('gulp');

// load all plugins with prefix 'gulp'
const $ = require('gulp-load-plugins')();

const sequence    = require('run-sequence');
const browserSync = require('browser-sync');

// Load build helpers
const _ = require('./asset/helpers')(gulp, browserSync);

/* Task: Compile SCSS
 --------------------------------------------------------------------------------- */

gulp.task('build:styles', () => {
    _.configs.sass.includePaths = [
        `${_.paths.src}vendor`,
        _._getDepsDir()
    ];

    const asset = gulp.src(_.paths.styles, { base: _.paths.src })
        // .pipe($.sourcemaps.init())
        .pipe($.sass(_.configs.sass).on('error', $.sass.logError))
        .pipe($.autoprefixer(_.configs.autoprefixer))
        // .pipe($.sourcemaps.write())
        .pipe($.cleanCss())
        .on('error', _.errorHandler);

    return _.build(asset);
});



/* Task: Minify JS
 --------------------------------------------------------------------------------- */

gulp.task('build:scripts', () => {
    const asset = gulp.src(_.paths.scripts, { base: _.paths.src })
        // .pipe($.sourcemaps.init())
        .pipe($.babel({ presets: ['es2015'] }))
        .on('error', _.errorHandler)
        // .pipe($.sourcemaps.write())
        .pipe($.uglify(_.configs.uglify))
        .on('error', _.errorHandler);

    return _.build(asset);
});



/* Task: Optimize image
 --------------------------------------------------------------------------------- */

gulp.task('build:images', () => {
    const asset = gulp.src(_.paths.images, { base: _.paths.src })
        .pipe($.changed(_.paths.dest))
        .pipe($.imagemin(_.configs.imagemin))
        .on('error', _.errorHandler);

    return _.build(asset);
});



/* Task: Optimize image
 --------------------------------------------------------------------------------- */

gulp.task('build:fonts', (done) => {
    const path = require('path');

    gulp.src(_.paths.fonts)
        .pipe($.changed(_.paths.dest))
        .pipe(gulp.dest((file) => {
            file.path = file.base + path.basename(file.path);
        return _.paths.dest + 'fonts/';
    }));

    return done();
});



/* Task: Modernizr
 --------------------------------------------------------------------------------- */

gulp.task('modernizr', function () {
    const conf = _.configs.modernizr;

    return gulp.src(_.paths.src + '**/*.{js,scss}')
        .pipe($.modernizr(conf.filename, conf.options))
        .pipe($.uglify(_.configs.uglify))
        .on('error', _.errorHandler)
        .pipe(gulp.dest(_.paths.dest + 'vendor/'));
});



/* Task: Serve
 --------------------------------------------------------------------------------- */

gulp.task('serve', ['build'], () => {
    const connect = require('gulp-connect-php');
    const sync = browserSync.init({
        port: _.configs.port,
        host: _.configs.host,
        proxy: { target: _.configs.url },
        open: 'open' in _.configs.serve ? _.configs.serve.open : false,
        logConnections: false
    });

    // Let's assume that you already setup your app server vhost
    if (_.configs.url.indexOf('localhost') !== -1) {
        return connect.server(_.configs.server, () => {
            return sync;
        });
    }

    return sync;
});



/* Task: Watch
 --------------------------------------------------------------------------------- */

gulp.task('watch', ['serve'], (done) => {
    // SCSS
    gulp.watch(_.paths.styles,  ['build:styles']);
    // Uglify
    gulp.watch(_.paths.scripts, ['build:scripts']);
    // Imagemin
    gulp.watch(_.paths.images,  ['build:images']);
    // Reload
    gulp.watch(_.configs.patterns.server)
        .on('change', browserSync.reload);

    // Done
    return done();
});



/* Task: Serve
 --------------------------------------------------------------------------------- */

gulp.task('wdio', (done) => {
    const exec = require('child_process').exec;
    const conf = {
        project: 'Creasi CMS',
        build: '',
        user: process.env.BROWSERSTACK_USER,
        key: process.env.BROWSERSTACK_KEY,
        baseUrl: _.configs.url,
        host: 'hub.browserstack.com',
        debug: true,
        forcelocal: process.env.APP_ENV == 'local',
        'browserstack.debug': true,
        'browserstack.local': process.env.APP_ENV == 'local'
    };

    exec('git rev-parse --short HEAD', { cwd: '.' }, (err, out) => {
        conf.build = out;
    });

    gulp.src('./asset/webdriver.js')
        .pipe($.webdriver(conf));

    return done();
});



/* Task: Clean
 --------------------------------------------------------------------------------- */

gulp.task('clean', (done) => {
    const del = require('del');

    del(_.paths.dest + _.configs.patterns.assets).then(() => {
        _.e('Assets directory cleaned', 'green');
    });

    return done();
});



/* Task: Build
 --------------------------------------------------------------------------------- */

gulp.task('build', (done) => {
    return sequence('build:styles', 'build:fonts', 'build:scripts', 'build:images', done);
});



/* Task: Default
 --------------------------------------------------------------------------------- */

gulp.task('default', ['clean', 'build']);
