module.exports = function(grunt) {

	// configure the tasks
	grunt.initConfig({

		uglify: { // Task
			all: {
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
				tasks: [ 'newer:uglify:all', 'beep' ]
			},
			
			// CSS
			sass: {
				files: 'app/assets/scss/**/*.scss',
				tasks: [ 'sass:all', 'beep' ]
			},

			// Grunt
			grunt: {
				files: ['Gruntfile.js'],
				tasks: [ 'beep' ]
			}
		}

	});

	// load the loadNpmTasks
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-newer');
 
	// define the tasks
 
	grunt.registerTask('default', [ 'watch' ]);

	grunt.registerTask('beep', function() { console.log('\x07'); });

};