var gulp          = require('gulp');
// var browserSync   = require('browser-sync').create();
var $             = require('gulp-load-plugins')();
var autoprefixer  = require('autoprefixer');

var sassPaths = [
    'node_modules/foundation-sites/scss',
    'node_modules/motion-ui/src',
    'node_modules/@moncareyws/foundation-perfect-scrollbar/src/scss/plugin',
    'node_modules/@moncareyws/foundation-select/src/scss/plugin',
    'node_modules/@fortawesome/fontawesome-free/scss'
];

var jsPaths = [
    'js/*.js',
    'node_modules/jquery/dist/jquery.min.js',
    'node_modules/what-input/dist/what-input.min.js',
    'node_modules/foundation-sites/dist/js/foundation.min.js',
    "node_modules/perfect-scrollbar/dist/js/perfect-scrollbar.jquery.min.js",
    "node_modules/@moncareyws/foundation-perfect-scrollbar/dist/js/foundation.perfectScrollbar.min.js",
    "node_modules/@moncareyws/foundation-select/dist/js/foundation.select.min.js"
];

function sass() {
  return gulp.src('scss/app.scss')
    .pipe($.sass({
      includePaths: sassPaths,
      outputStyle: 'nested' // if css compressed **file size**
    })
      .on('error', $.sass.logError))
    .pipe($.postcss([
      autoprefixer({ browsers: ['last 2 versions', 'ie >= 9'] })
    ]))
    .pipe(gulp.dest('../public/css'));
    // .pipe(browserSync.stream());
}

function js() {
  return gulp.src(jsPaths)
      .pipe(gulp.dest('../public/js'));
}

function watch() {
  // browserSync.init({
  //   server: "./"
  // });

  gulp.watch(["scss/*.scss","scss/*.sass"], sass);
  gulp.watch(jsPaths, js);
  // gulp.watch("*.html").on('change', browserSync.reload);
}

gulp.task('sass', sass);
gulp.task('js', js);
gulp.task('default', gulp.series('sass', 'js', watch));
