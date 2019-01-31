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
 * @package danfoy-2019
 * @author  Dan Foy (danfoy.com)
 * @since   1.0.0
 */


/**
 * Load Gulp and required plugins
 *
 * Plugins must also be installed to `./node_modules`.
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
 * Directories
 *
 * Occasionally I'll need to move a directory. Referencing the directories via
 * this object means I only have to change one line of code for each movement.
 */
const dir = {
    src:        'src/',                         // Source directory
    notsrc:     '!src/',                        // Negated for globbing
    dest:       'content/themes/danfoy_2019/',  // Output directory
    vagrant:    'chassis/'                      // Vagrant directory
};


/**
 * Process Stylesheets
 *
 * Takes a master SASS document and processes it into a production-ready
 * `style.css` in the root of `./dist` as per WordPress theme requirements.
 *
 * Note there is no file globbing here. If a partial doesn't have an `@include`
 * statement in `src/global/master.scss`, it doesn't appear in `dist/style.css`.
 */
gulp.task('css', () => {
    return gulp.src(dir.src + 'master.scss')    // Master stylesheet index
    .pipe(plumber())                            // Handle errors
    .pipe(sourcemaps.init())                    // Start sourcemapping:
        .pipe(sass({                            // - Process SCSS:
            outputStyle: 'nested',              //   - SCSS-like (for debugging)
            precision: 3,                       //   - Decimal places
            errLogToConsole: true               //   - Show errors in console
        }))
        .pipe(postcss([                         // - Further processing:
            autoprefixer({                      //   - Add vendor prefixes
                browsers: [                     //   - For browserlist:
                    'last 2 versions',          //     - Major releases
                    '> 2%'                      //     - Still in the wild
                ]
            }),
            cssnano()                           // - Minify output
        ]))
        .pipe(concat('style.css'))              // - Rename to `style.css`
    .pipe(sourcemaps.write('.'))                // Generate external sourcemap
    .pipe(gulp.dest(dir.dest))                  // Write file
    .pipe(browsersync.reload({stream:true}));   // Reload BrowserSync
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
 */
gulp.task('php', () => {
    return gulp.src(dir.src + '**/*.php')       // Get all PHP files
    .pipe(plumber())                            // Handle errors
    .pipe(newer(dir.dest))                      // Target changed files only
    .pipe(trim())                               // Remove L/R whitespace
    .pipe(rename({dirname: ''}))                // Flatten filetree
    .pipe(gulp.dest(dir.dest));                 // Write files
});


/**
 * Process JavaScript files
 *
 * For now this just copies all JS files into `./dist/js/`.
 *
 * TODO: Add minification and sourcemapping
 */
gulp.task('js', () => {
    return gulp.src(dir.src + '**/*.js')        // Get all JavaScript files
    .pipe(plumber())                            // Handle errors
    .pipe(newer(dir.dest + 'js/'))              // Target changed files only
    .pipe(rename({dirname: ''}))                // Flatten filestructure
    .pipe(gulp.dest(dir.dest + 'js/'));         // Write files
});


/**
 * Copy images
 * (except screenshot - see below)
 *
 * Copy all images into `./dist/img/`
 *
 * TODO: Add minification, maybe SVG -> GIF fallbacks or similar
 */
gulp.task('img', () => {
    return gulp.src([
        dir.src + 'img/**/*',                   // Get everything in img/
        dir.notsrc + 'img/{assets,assets/**}',  // Ignore reference/src assets
        dir.notsrc + 'img/screenshot.png'       // Ignore screenshot
        ])
    .pipe(plumber())                            // Handle errors
    .pipe(newer(dir.dest + 'img/'))             // Target changed files only
    .pipe(gulp.dest(dir.dest + 'img/'));        // Write files
});


/**
 * Copy screenshot
 *
 * WordPress expects the screenshot for the Theme Picker to be in the theme's
 * root directory, so copy this over separately
 */
gulp.task('screenshot', () => {
    return gulp.src(gulp.src + 'img/screenshot.png')
    .pipe(plumber())                            // Handle errors
    .pipe(newer(dir.dest))                      // Check if changed
    .pipe(gulp.dest(dir.dest));                 // Write file
});


/**
 * BrowserSync config
 *
 * Magical system for automatically updating CSS and JS changes, and reloading
 * on PHP changes.
 */
gulp.task('sync', () => {
    browsersync.init({                          // Initialize with settings:
        proxy: 'danfoy.local',                  // - Address to proxy
        files: dir.dest + '**/*',               // - Files to monitor
        open: false,                            // - Start browser
        notify: false,                          // - Flash element on change
        reloadOnRestart: true,                  // - Restart after crash
        ghostMode: false,                       // - Browser mirroring
        ui: {                                   // - Interface:
            port: 8001                          //   - Port
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
 */
gulp.task('config', () => {
    return gulp.src([                           // Get these files specifically:
        'config.local.yaml',                    // - Chassis configuration
        'local-config.php'                      // - wp-config.php overrides
    ])
    .pipe(plumber())                            // Handle errors
    .pipe(newer(dir.vagrant))                   // Target only changed files
    .pipe(gulp.dest(dir.vagrant))               // Write files
});


/**
 * Purge files
 *
 * Wipe `./dist/`. Good hygine, removes duplicate and crufty files after file
 * location changes etc. Also useful for debugging.
 */
gulp.task('purge', () => {
    return gulp.src([dir.dest],                 // Get all output files:
        {read: false})                          // - Don't bother reading them
    .pipe(plumber())                            // Handle errors
    .pipe(clean());                             // Wipe files
});


/**
 * Default task
 *
 * What happens when `gulp` is run by itself at command line.
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
 */
gulp.task('watch', ['sync'], () => {            // BrowserSync as dependency
    gulp.watch(dir.src + '**/*.php', ['php'],   // Watch PHP files:
        browsersync.reload);                    // - reload on changes
    gulp.watch(dir.src + '**/*.scss', ['css']); // Watch stylesheets
    gulp.watch(dir.src + 'js/*.js', ['js']);    // Watch scripts
    gulp.watch([                                // Watch config files:
            'config.local.yaml',                // - Chassis configuration
            'local-config.php'                  // - wp-config.php overrides
        ], ['config']);
});
