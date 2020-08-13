var gulp = require('gulp');

// Include plugins (from package.json)
var plugins = require('gulp-load-plugins')();

//path
var source = './src'; // dossier de travail
var destination = './dist';

gulp.task('css', function () {
    return gulp.src('scss/*.scss')
    /* ici les plugins Gulp à exécuter */
    .pipe(gulp.dest('css'));
});