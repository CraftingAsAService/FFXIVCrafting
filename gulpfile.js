var gulp = require('gulp'),
	fs = require('fs'),
	debounce = require('debounce'),
	gulpLoadPlugins = require('gulp-load-plugins'),
	plugins = gulpLoadPlugins({
		rename: {
			'gulp-sass-multi-inheritance': 'sassInheritance'
		}
	});

function getStatus() {
	// If the gulp.stop file exists, don't run any gulp commands
	// Used in conjunction with a git alias/function that creates and deletes this file
	if (fs.existsSync('gulp.stop')) {
		console.log('gulp.stop - Gulp Prevented From Running');
		return false;
	}
	return true;
}

gulp.task('watch', function() {
	global.isWatching = true;

	/**
	 * CSS
	 */
	gulp.watch('resources/scss/**/*.scss', debounce(gulp.parallel('css')));

	/**
	 * JavaScript
	 */
	gulp.watch('resources/js/**/*.js', debounce(gulp.parallel('js')));
});

/**
 * SASS
 */
gulp.task('css', function(done) {
	if ( ! getStatus())
		return done();

	return gulp.src('resources/scss/**/*.scss')
		.pipe(plugins.plumber({ errorHandle: plugins.notify.onError("Error: <%= error.message %>") }))
		// filter out unchanged scss files, only works when watching
		.pipe(plugins.if(global.isWatching, plugins.cached('sass')))
		// Find files that depend on the files that have changed
		// Also finds files inside of ctgus, as they can depend on common assets
		.pipe(plugins.sassInheritance({ dir: 'resources/scss' }))
		// Filter out internal imports (folders and files starting with "_" )
		.pipe(plugins.filter(function (file) {
			return !/\/_/.test(file.path) || !/^_/.test(file.relative);
		}))
		// Run SASS and AutoPrefix it
		.pipe(plugins.sass({ outputStyle: 'compressed' }).on('error', plugins.sass.logError))
		.pipe(plugins.autoprefixer())
		// Save file down and notify
		.pipe(gulp.dest('public/css'))
		.pipe(plugins.notify({ message: 'Sass compiled <%= file.relative %>' }));
});

/**
 * Scripts
 */
gulp.task('js', function(done) {
	if ( ! getStatus())
		return done();

	return gulp.src('resources/js/**/*.js')
		.pipe(plugins.plumber({ errorHandle: plugins.notify.onError("Error: <%= error.message %>") }))
		// filter out unchanged js files, only works when watching
		.pipe(plugins.if(global.isWatching, plugins.cached('terser')))
		.pipe(plugins.terser())
		.pipe(gulp.dest('public/js'))
		.pipe(plugins.notify({ message: 'JS compiled <%= file.relative %>' }));
});