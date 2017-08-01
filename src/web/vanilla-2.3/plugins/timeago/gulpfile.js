var gulp = require('gulp')
  , concat = require('gulp-concat')
  , uglify = require('gulp-uglify');

gulp.task('concat', function () {
  gulp.src([
    'bower_components/jquery-timeago/jquery.timeago.js'
  , 'js/initialize.js'
  ])
    .pipe(uglify())
    .pipe(concat('timeago.min.js'))
    .pipe(gulp.dest('js'));
});

gulp.task('watch', function () {
  gulp.watch(['js/**', '!js/timeago.min.js'], ['concat']);
});
