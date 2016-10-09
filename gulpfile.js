/* Gulp set up
 --------------------------------------------------------------------------------- */

const gulp = require('gulp');

// load all plugins with prefix 'gulp'
const $ = require('gulp-load-plugins')();

const connect  = require('gulp-connect-php');
const sequence = require('run-sequence');

const _ = require('./assets/build')(gulp);

/* Task: Compile SCSS
 --------------------------------------------------------------------------------- */

gulp.task('build:styles', ['lint:styles'], () => {
    _.conf.sass.includePaths = _.depsDir;

    const styles = gulp.src(_.paths.styles, {base: _.paths.src})
        .pipe($.sass(_.conf.sass).on('error', $.sass.logError))
        .pipe($.autoprefixer(_.conf.autoprefixer))
        .pipe($.cleanCss())
        .on('error', _.errorHandler);

    return _.build(styles);
});

/* Task: Minify JS
 --------------------------------------------------------------------------------- */

gulp.task('build:scripts', ['lint:scripts'], () => {
    const scripts = gulp.src(_.paths.scripts, {base: _.paths.src})
        .pipe($.babel({presets: ['es2015']}))
        .on('error', _.errorHandler)
        .pipe($.uglify(_.conf.uglify))
        .on('error', _.errorHandler);

    return _.build(scripts);
});

/* Task: Optimize image
 --------------------------------------------------------------------------------- */

gulp.task('build:images', () => {
    const images = gulp.src(_.paths.images, {base: _.paths.src})
        .pipe($.changed(_.paths.dest))
        .pipe($.imagemin(_.conf.imagemin))
        .on('error', _.errorHandler);

    return _.build(images);
});

/* Task: Optimize image
 --------------------------------------------------------------------------------- */

gulp.task('build:fonts', (done) => {
    const path = require('path');

    gulp.src(_.paths.fonts, {base: _.paths.src})
        .pipe(gulp.dest((file) => {
            file.path = file.base + path.basename(file.path);

            return _.paths.dest + 'fonts/';
        }));

    return done();
});

/* Task: Lint SCSS
 --------------------------------------------------------------------------------- */

gulp.task('lint:styles', () => {
    _.conf.sass.includePaths = _.depsDir;

    return gulp.src(_.paths.styles, {base: _.paths.src})
        .pipe($.sassLint(_.conf.sasslint))
        .pipe($.sassLint.format())
        .pipe($.sassLint.failOnError());
});

/* Task: Lint JS
 --------------------------------------------------------------------------------- */

gulp.task('lint:scripts', () => {
    return gulp.src(_.paths.scripts, {base: _.paths.src})
        .pipe($.eslint(_.conf.eslint))
        .pipe($.eslint.format())
        .pipe($.eslint.failOnError());
});

/* Task: Vendor
 --------------------------------------------------------------------------------- */

gulp.task('vendor:copy', ['modernizr'], () => {
    return gulp.src(_.vendors)
        .pipe(gulp.dest(_.paths.vendor));
});

/* Task: Minify Vendor scripts
 --------------------------------------------------------------------------------- */

gulp.task('vendor:scripts', () => {
    return gulp.src(_.paths.vendor + '/**/*.js')
        .pipe($.uglify(_.conf.uglify))
        .on('error', _.errorHandler)
        .pipe(gulp.dest(_.paths.vendor));
});

/* Task: Minify Vendor scripts
 --------------------------------------------------------------------------------- */

gulp.task('vendor:styles', () => {
    return gulp.src(_.paths.vendor + '/**/*.css')
        .pipe($.cleanCss())
        .on('error', _.errorHandler)
        .pipe(gulp.dest(_.paths.vendor));
});

/* Task: Modernizr
 --------------------------------------------------------------------------------- */

gulp.task('modernizr', () => {
    const conf = _.conf.modernizr;

    return gulp.src(_.paths.src + '**/*.{js,scss}')
        .pipe($.modernizr(conf.filename, conf.settings))
        .pipe(gulp.dest(_.paths.vendor));
});

/* Task: Serve
 --------------------------------------------------------------------------------- */

gulp.task('serve', () => {
    if (_.isLocal) {
        return connect.server(_.server, _.sync);
    }

    return _.sync();
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
        .on('change', _.bSync.reload);

    // Done
    return done();
});

/* Task: Test Behaviour
 --------------------------------------------------------------------------------- */

gulp.task('test:bdd', (done) => {
    gulp.src('./tests/webdriver.js')
        .pipe($.webdriver(_.wdio));

    return done();
});

/* Task: Clean
 --------------------------------------------------------------------------------- */

gulp.task('clean', () => {
    const del = require('del');

    return del([_.paths.dest, _.paths.vendor]).then(() => {
        _.e('Assets directory cleaned', 'green');
    });
});

/* Task: Vendor
 --------------------------------------------------------------------------------- */

gulp.task('vendor', (done) => {
    sequence('vendor:copy', 'vendor:scripts', 'vendor:styles', done);
});

/* Task: Build
 --------------------------------------------------------------------------------- */

gulp.task('build', (done) => {
    sequence('build:styles', 'build:scripts', 'build:images', 'build:fonts', done);
});

/* Task: Lint
 --------------------------------------------------------------------------------- */

gulp.task('lint', ['lint:styles', 'lint:scripts']);

/* Task: Default
 --------------------------------------------------------------------------------- */

gulp.task('default', (done) => {
    sequence('clean', 'build', 'vendor', done);
});
