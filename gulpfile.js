var gulp = require('gulp'),
	fs = require('fs'),
	gulpLoadPlugins = require('gulp-load-plugins'),
	plugins = gulpLoadPlugins({
		rename: {
			'gulp-sass-multi-inheritance': 'sassInheritance'
		}
	});

const SANE_OPTIONS = { debounceDelay: 2000, saneOptions: { watchman: true } };

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
	plugins.saneWatch('resources/assets/scss/**/*.scss', SANE_OPTIONS, function() {
		gulp.start('css');
	});

	/**
	 * JavaScript
	 */
	plugins.saneWatch('resources/assets/javascript/**/*.js', SANE_OPTIONS, function() {
		gulp.start('js');
	});
});

/**
 * SASS
 */
gulp.task('css', function() {
	if ( ! getStatus())
		return;

	return gulp.src('resources/assets/scss/**/*.scss')
		.pipe(plugins.plumber({ errorHandle: plugins.notify.onError("Error: <%= error.message %>") }))
		// Find files that depend on the files that have changed
		// Also finds files inside of ctgus, as they can depend on common assets
		.pipe(plugins.sassInheritance({ dir: 'resources/assets/scss' }))
		// Filter out internal imports (folders and files starting with "_" )
		.pipe(plugins.filter(function (file) {
			return !/\/_/.test(file.path) || !/^_/.test(file.relative);
		}))
		// filter out unchanged scss files, only works when watching
		.pipe(plugins.if(global.isWatching, plugins.cached('sass')))
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
gulp.task('js', function() {
	if ( ! getStatus())
		return;

	return gulp.src('resources/assets/javascript/**/*.js')
		.pipe(plugins.plumber({ errorHandle: plugins.notify.onError("Error: <%= error.message %>") }))
		// filter out unchanged js files, only works when watching
		.pipe(plugins.if(global.isWatching, plugins.cached('uglify')))
		.pipe(plugins.uglify())
		.pipe(gulp.dest('public/js'))
		.pipe(plugins.notify({ message: 'JS compiled <%= file.relative %>' }));
});