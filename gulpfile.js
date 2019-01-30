/**
 * gulpfile for danfoy-2019 WordPress theme
 *
 * Builds the danfoy-2019 WordPress theme using gulpjs.
 *
 * All files for the theme go into `./dist`. Upload the contents of this
 * directory via FTP, or copy-rename to `danfoy-2019`.
 *
 * Some additional files are compiled to `./dev` to aid local development.
 *
 * @package df19
 * @author Dan Foy (danfoy.com)
 *
 */


/**
 * Load Gulp and required plugins
 *
 * Plugins must also be installed to `./node_modules`.
 *
 */
const
    gulp                = require('gulp'),
    concat              = require('gulp-concat'),
    rename              = require('gulp-rename'),
    clean               = require('gulp-clean'),
    trim                = require('gulp-trimlines'),
    newer               = require('gulp-newer'),
    passthru            = require('gulp-noop'),
    plumber             = require('gulp-plumber'),
    sass                = require('gulp-sass'),
    sourcemaps          = require('gulp-sourcemaps'),
    browsersync         = require('browser-sync').create(),
    postcss             = require('gulp-postcss'),
        autoprefixer    = require('autoprefixer'),
        cssnano         = require('cssnano')
;


/**
 * Process Stylesheets
 *
 * Takes a master SASS document and processes it into a production-ready
 * `style.css` in the root of `./dist` as per WordPress theme requirements.
 *
 * Note there is no file globbing here. If a partial doesn't have an `@include`
 * statement in `src/global/master.scss`, it doesn't appear in `dist/style.css`.
 *
 */
gulp.task('css', () => {
    return gulp.src('src/master.scss')   // list of `@include`s
    .pipe(plumber())                            // Fail gracefully
    .pipe(sourcemaps.init())                    // Start sourcemapping
        .pipe(sass({                            // Process SCSS
            outputStyle: 'nested',                  // SCSS-like (for debugging)
            precision: 3,                           // Decimal places
            errLogToConsole: true                   // Show errors in console
        }))
        .pipe(postcss([                         // Further processing:
            autoprefixer({                          // Add vendor prefixes
                browsers: [                             // For browserlist:
                    'last 2 versions',                      // Major releases
                    '> 2%'                                  // Still in usage
                ]
            }),
            cssnano()                               // Minify output
        ]))
        .pipe(concat('style.css'))              // Rename to `style.css`
    .pipe(sourcemaps.write('.'))                // Generate external sourcemap
    .pipe(gulp.dest('dist'))                   // Write to root of `./dist/`
    .pipe(browsersync.reload({stream:true}));
});


/**
 * Copy PHP files
 *
 * The filestructure in `./src/` is very modularised, so there is a wide glob
 * which takes all PHP files from any folders and flattens them into the root of
 * `./dist/`, which is what WordPress wants.
 *
 * WordPress also makes a terrible mess of indentation, so there is a hard trim.
 * I could pretend this is for minification purposes but honestly I just find
 * the flattened output less painful to look at.
 *
 */
gulp.task('php', () => {
    return gulp.src('src/**/*.php')             // Get all PHP files
    .pipe(plumber())                            // Fail gracefully
    .pipe(newer('dist'))                        // Target changed files only
    .pipe(trim())                               // Remove L/R whitespace
    .pipe(rename({dirname: ''}))                // Flatten filetree
    .pipe(gulp.dest('dist'));                   // Write files
});


/**
 * Process JavaScript files
 *
 * For now this just copies all JS files into `./dist/js/`.
 *
 * TODO: Add minification and sourcemapping
 *
 */
gulp.task('js', () => {
    return gulp.src('src/**/*.js')              // Get all JavaScript files
    .pipe(plumber())                            // Fail gracefully
    .pipe(newer('dist/js'))                     // Target changed files only
    .pipe(rename({dirname: ''}))                // Flatten filestructure
    .pipe(gulp.dest('dist/js/'));               // Write files
});


/**
 * Copy images
 * (except screenshot - see below)
 *
 * Copy all images into `./dist/img/`
 *
 * TODO: Add minification, maybe SVG -> GIF fallbacks or similar
 *
 */
gulp.task('img', () => {
    return gulp.src([
        'src/img/**/*',                         // Get contents of `./src/img/`
        '!src/img/{assets,assets/**}',          // Ignore reference/src assets
        '!src/img/screenshot.png'               // Ignore screenshot
        ])
    .pipe(plumber())                            // Fail gracefully
    .pipe(newer('dist/img/'))                   // Target changed files only
    .pipe(gulp.dest('dist/img/'));              // Write files
});


/**
 * Copy screenshot
 *
 * WordPress expects the screenshot for the Theme Picker to be in the theme's
 * root directory, so copy this over separately
 *
 */
gulp.task('screenshot', () => {
    return gulp.src('src/img/screenshot.png')   // Get screenshot
    .pipe(plumber())                            // Fail gracefully
    .pipe(newer('dist/'))                       // Check if changed
    .pipe(gulp.dest('dist/'));                  // Write file
});


/**
 * BrowserSync config
 *
 * Magical system for automatically updating CSS and JS changes, and reloading
 * on PHP changes.
 *
 */
gulp.task('sync', () => {
    browsersync.init({                      // Initialize with settings:
        proxy: 'danfoy.local',                  // Address to proxy
        files: 'dist/**/*',                     // Files to monitor
        open: false,                            // Start browser
        notify: false,                          // Flash element on change
        reloadOnRestart: true,                  // Restart after crash
        ghostMode: false,                       // Browser mirroring
        ui: {                                   // Interface
            port: 8001                              // ^ port
        }
    });
});

/**
 * Copy Chassis config files
 *
 * There are two configuration files required by Chassis that live inside the 
 * `chassis/` directory. These are destroyed if I need to re-clone the Chassis
 * repo, so better to store them with the rest of the config files and Gulp
 * them over.
 * 
 */
gulp.task('config', () => {
    return gulp.src([
            'config.local.yaml',            // Chassis configuration
            'local-config.php'              // wp-config.php overrides
        ])
    .pipe(plumber())                        // Fail gracefully
    .pipe(newer('chassis/'))                // Only copy newer files
    .pipe(gulp.dest('chassis/'))            // Copy files
});


/**
 * Purge files
 *
 * Wipe `./dist/`. Good hygine, removes duplicate and crufty files after file
 * location changes etc. Also useful for debugging.
 *
 */
gulp.task('purge', () => {
    return gulp.src(['dist/'], {read: false})   // Don't bother reading files
    .pipe(plumber())                            // Fail gracefully
    .pipe(clean());                             // Wipe everything
});


/**
 * Default task
 *
 * What happens when `gulp` is run by itself at command line.
 *
 */
gulp.task('default', [
    'php',
    'js',
    'css',
    'img',
    'screenshot'
    ]
);


/**
 * Watch task
 *
 * Start BrowserSync and watch files for changes. Standard development mode.
 *
 */
gulp.task('watch', ['sync'], () => {     // BrowserSync as dependency
    gulp.watch('src/**/*.php', ['php']);
    // page changes
    gulp.watch('src/**/*.php', ['php'], browsersync.reload);

    // CSS changes
    gulp.watch('src/**/*.scss', ['css']);

    // JavaScript main changes
    gulp.watch('src/js/*.js', ['js']);

    gulp.watch([
            'config.local.yaml',            // Chassis configuration
            'local-config.php'              // wp-config.php overrides
        ], ['config']);

});

function watch2(cb) {
    bsync();
    gulp.watch('src/**/*.php', ['php']);
    gulp.watch('dist/*.php').on('change', browsersync.reload);
    gulp.watch('src/**/*.scss', ['css']);
    gulp.watch('src/js/*.js', ['js']);

};

function bsync(cb){                 // Create BrowserSync instance
    browsersync.init({                      // Initialize with settings:
        proxy: 'danfoy.local',                  // Address to proxy
        files: 'dist/**/*',                     // Files to monitor
        open: false,                            // Start browser
        notify: false,                          // Flash element on change
        reloadOnRestart: true,                  // Restart after crash
        ghostMode: false,                       // Browser mirroring
        ui: {                                   // Interface
            port: 8001                              // ^ port
        }
    });
}

exports.watch2 = watch2;