var gulp = require('gulp');
var less = require('gulp-less');
var path = require('path');
var minifyCSS = require('gulp-minify-css');
var sourcemaps = require('gulp-sourcemaps');
var del = require('del');

gulp.task('less', function () {
    return gulp.src('./src/Aixia/PostitBoardFront/Resources/less/main.less')
        .pipe(less({
            paths: [ path.join(__dirname, './web/bower_components/bootstrap/less') ]
        }))
        .pipe(minifyCSS())
        .pipe(gulp.dest('./web/public/css'));
});


gulp.task('watch', function() {
    gulp.watch('src/Aixia/PostitBoardFront/Resources/less/**/*.less', ['less']);
});

