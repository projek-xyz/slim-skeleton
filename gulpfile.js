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

gulp.task('serve', ['watch'], () => {
    const sync = () => {
        browserSync.init({
            port: _.conf.port,
            host: _.conf.host,
            proxy: { target: _.conf.url },
            open: 'open' in _.conf.serve ? _.conf.serve.open : false,
            logConnections: false
        });
    };

    // Let's assume that you already setup your app server vhost
    if (_.isLocal) {
        connect.server(_.conf.server, sync);
    } else {
        sync();
    }
});



/* Task: Watch
 --------------------------------------------------------------------------------- */

gulp.task('watch', ['build'], (done) => {
    // SCSS & Minify
    gulp.watch(_.paths.styles,  ['build:styles']);
    // ES2015 & Uglify
    gulp.watch(_.paths.scripts, ['build:scripts']);
    // Imagemin
    gulp.watch(_.paths.images,  ['build:images']);
    // Reload
    gulp.watch(_.conf.patterns.server)
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
        baseUrl: _.conf.url,
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
