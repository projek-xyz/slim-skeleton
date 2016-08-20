/* Gulp set up
 --------------------------------------------------------------------------------- */

var gulp = require('gulp');

// load all plugins with prefix 'gulp'
const $ = require('gulp-load-plugins')();

const connect     = require('gulp-connect-php');
const sequence    = require('run-sequence');
const browserSync = require('browser-sync');

const _ = require('./asset/helpers')(gulp, browserSync);

/* Task: Compile SCSS
 --------------------------------------------------------------------------------- */

gulp.task('build:styles', () => {
    _.conf.sass.includePaths = _.depsDir;

    const asset = gulp.src(_.paths.styles, { base: _.paths.src })
        .pipe($.sass(_.conf.sass).on('error', $.sass.logError))
        .pipe($.autoprefixer(_.conf.autoprefixer))
        .pipe($.cleanCss())
        .on('error', _.errorHandler);

    return _.build(asset);
});



/* Task: Minify JS
 --------------------------------------------------------------------------------- */

gulp.task('build:scripts', () => {
    const asset = gulp.src(_.paths.scripts, { base: _.paths.src })
        .pipe($.babel({ presets: ['es2015'] }))
        .on('error', _.errorHandler)
        .pipe($.uglify(_.conf.uglify))
        .on('error', _.errorHandler);

    return _.build(asset);
});



/* Task: Optimize image
 --------------------------------------------------------------------------------- */

gulp.task('build:images', () => {
    const asset = gulp.src(_.paths.images, { base: _.paths.src })
        .pipe($.changed(_.paths.dest))
        .pipe($.imagemin(_.conf.imagemin))
        .on('error', _.errorHandler);

    return _.build(asset);
});



/* Task: Optimize image
 --------------------------------------------------------------------------------- */

gulp.task('build:fonts', (done) => {
    const path = require('path');

    gulp.src(_.paths.fonts, { base: _.paths.src })
        .pipe($.changed(_.paths.dest))
        .pipe(gulp.dest((file) => {
            file.path = file.base + path.basename(file.path);
            return _.paths.dest + 'fonts/';
        }
    ));

    return done();
});



/* Task: Modernizr
 --------------------------------------------------------------------------------- */

gulp.task('modernizr', function () {
    const conf = _.conf.modernizr;

    return gulp.src(_.paths.src + '**/*.{js,scss}')
        .pipe($.modernizr(conf.filename, conf.options))
        .pipe($.uglify(_.conf.uglify))
        .on('error', _.errorHandler)
        .pipe(gulp.dest(_.paths.dest + 'vendor/'));
});



/* Task: Serve
 --------------------------------------------------------------------------------- */

gulp.task('serve', () => {
    // Let's assume that you already setup your app server vhost
    if (_.isLocal) {
        return _.serve();
    }

    _.sync();
});



/* Task: Watch
 --------------------------------------------------------------------------------- */

gulp.task('watch', ['serve'], (done) => {
    // SCSS & Minify
    gulp.watch(_.paths.styles,  ['build:styles']);
    // ES2015 & Uglify
    gulp.watch(_.paths.scripts, ['build:scripts']);
    // Imagemin
    gulp.watch(_.paths.images,  ['build:images']);
    // Reload
    gulp.watch(_.conf.patterns.server)
        .on('change', _._bs.reload);

    // Done
    return done();
});



/* Task: Test Behaviour
 --------------------------------------------------------------------------------- */

gulp.task('test:bdd', (done) => {
    gulp.src('./asset/webdriver.js')
        .pipe($.webdriver(_.wdio));

    return done();
});



/* Task: Clean
 --------------------------------------------------------------------------------- */

gulp.task('clean', (done) => {
    const del = require('del');

    del(_.paths.dest + _.conf.patterns.assets).then(() => {
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
