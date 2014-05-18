module.exports = function(grunt) {

	// Include the external Gruntconfig
	// grunt.util._.extend(config, loadConfig('localconfig.json'));

	// configure the tasks
	grunt.initConfig({

		// cloudfiles: {
		// 	production: {

		// 	}
		// }

		imagemin: {
			all: {
				files: [{
					expand: true,
					cwd: 'public/img',
					src: ['**/*.{png,gif,PNG,GIF,jpg,jpeg,JPG,JPEG}'],
					dest: 'public/img'
				}]
			}
		},

		uglify: { // Task
			all: {
				options: { // Target Options
					// beautify: true, // DEBUGGING
					// mangle: false
				},
				files: [{
					expand: true,
					cwd: 'app/assets/javascript',
					src: [ '**/*.js' ],
					dest: 'public/js'
				}]
			},
		},

		sass: {
			all: { // Target
				options: {
					style: 'compressed'
				},
				files: [{
					expand: true,
					cwd: 'app/assets/scss',
					src: [ '**/*.scss', '!**/_*.scss' ],
					dest: 'public/css',
					ext: '.css'
				}]
			},
		},

		watch: {
			// Javascript
			uglify: {
				files: 'app/assets/javascript/**/*.js',
				tasks: [ 'newer:uglify:all', 'beep' ],
				options: { interval: 5007 }
			},
			
			// CSS
			sass: {
				files: 'app/assets/scss/**/*.scss',
				tasks: [ 'sass:all', 'beep' ],
				options: { interval: 5007 }
			},

			// Grunt
			grunt: {
				files: ['Gruntfile.js'],
				tasks: [ 'beep' ],
				options: { interval: 5007 }
			}
		}

	});

	// load the loadNpmTasks
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-imagemin');
	grunt.loadNpmTasks('grunt-newer');
 
	// define the tasks
 
	grunt.registerTask('default', [ 'watch' ]);
	grunt.registerTask('images', [ 'newer:imagemin:all', 'beep' ]);
	// grunt.registerTask('cdn', [ 'newer:cloudfiles:production', 'beep' ]);

	grunt.registerTask('beep', function() { console.log('\x07'); });

};