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
					cwd: 'resources/assets/javascript',
					src: [ '**/*.js' ],
					dest: 'public/js'
				}]
			},
		},

		sass: {
			all: { // Target
				options: {
					style: 'compressed',
					sourcemap: 'none'
				},
				files: [{
					expand: true,
					cwd: 'resources/assets/scss',
					src: [ '**/*.scss' ],
					dest: 'public/css',
					ext: '.css'
				}]
			},
		},

		watch: {
			// Javascript
			uglify: {
				files: 'resources/assets/javascript/**/*.js',
				tasks: [ 'newer:uglify:all', 'notify:uglify' ]//, 'beep' ]
			},
			
			// CSS
			sass: {
				files: 'resources/assets/scss/**/*.scss',
				tasks: [ 'sass:all', 'notify:sass' ]//, 'beep' ]
			},

			// Grunt
			// grunt: {
			// 	files: ['Gruntfile.js'],
			// 	tasks: [ 'beep' ]
			// }
		},

		notify: {
			uglify: {
				options: {
					title: 'JS Uglified',
					message: 'Task Complete'
				}
			},
			sass: {
				options: {
					title: 'SASS Compiled',
					message: 'Task Complete'
				}
			}
		},

		notify_hooks: {
			options: {
				enabled: true,
				max_jshint_notifications: 5,
				title: 'FFXIV Crafting Grunt',
				success: true,
				duration: 3
			}
		}

	});

	// load the loadNpmTasks
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-imagemin');
	grunt.loadNpmTasks('grunt-newer');
	grunt.loadNpmTasks('grunt-notify');
 
	// define the tasks
 
	grunt.registerTask('default', [ 'watch' ]);
	grunt.registerTask('images', [ 'newer:imagemin:all' ]);//, 'beep' ]);
	// grunt.registerTask('cdn', [ 'newer:cloudfiles:production', 'beep' ]);

	// grunt.registerTask('beep', function() { console.log('\x07'); });

};