module.exports = function(grunt) {

	// configure the tasks
	grunt.initConfig({

		uglify: { // Task
			caas: {
				files: [{
					expand: true,
					cwd: 'app/assets/javascript',
					src: [ '**/*.js' ],
					dest: 'public/js'
				}]
			},
		},

		sass: {
			caas: { // Target
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
			caas_js: {
				files: 'app/assets/javascript/**/*.js',
				tasks: [ 'uglify:caas', 'beep' ]
			},
			
			// CSS
			caas_css: {
				files: 'app/assets/scss/**/*.scss',
				tasks: [ 'sass:caas', 'beep' ]
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
 
	// define the tasks
 
	grunt.registerTask('default', [ 'watch' ]);

	grunt.registerTask('beep', function() { console.log('\x07'); });

};