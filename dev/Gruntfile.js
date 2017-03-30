(function() {
	'use strict';
	module.exports = function(grunt) {
		grunt
				.initConfig({
					pkg : grunt.file.readJSON('package.json'),
					
					project : {
						public : [ '../public' ],
						dev : [ './' ],
						assets : [ '<%= project.dev %>/assets' ],
						build : [ '<%= project.assets %>/build' ],
						dist : [ '<%= project.assets %>/dist' ],
						scss : [ '<%= project.assets %>/sass/style.scss' ],
						bs : [ '<%= project.dev %>/bower_components/bootstrap-sass/assets' ],
						obs : [ '<%= project.dev %>/bower_components/bootstrap-sass-official/assets' ],
						angular : [ '<%= project.dev %>/bower_components/angular' ],
						slider : [ '<%= project.dev %>/bower_components/seiyria-bootstrap-slider' ],
						aslider : [ '<%= project.dev %>/bower_components/angular-bootstrap-slider' ],
						abs : [ '<%= project.dev %>/bower_components/angular-bootstrap' ]
					},
					// Copy web assets from bower_components to more convenient directories.
					copy : {
						stage : {
							files : [
									// BS Vendor scripts.
									{
										cwd : '<%= project.obs %>/javascripts/',
										src : [ '*.js' ],
										expand : true,
										filter : 'isFile',
										dest : '<%= project.build %>/js/bootstrap'
									},
									
									{
										cwd : '<%= project.abs %>/',
										src : [ 'ui-bootstrap.tpls.js' ],
										expand : true,
										filter : 'isFile',
										dest : '<%= project.public %>/js'
									},
									
									// BS Fonts.
									{
										cwd : '<%= project.bs %>/fonts/',
										src : [ 'bootstrap/**' ],
										expand : true,
										dest : '<%= project.public %>/css/bootstrap'
									},
									
									// Angular Scripts.
									{
										cwd : '<%= project.angular %>/',
										src : [ 'angular.min.js' ],
										expand : true,
										filter : 'isFile',
										dest : '<%= project.public %>/js'
									},
									
									{
										cwd : '<%= project.slider %>/dist/',
										src : [ 'bootstrap-slider.min.js' ],
										expand : true,
										filter : 'isFile',
										dest : '<%= project.build %>/js/bootstrap-slider'
									},
									
									{
										cwd : '<%= project.aslider %>/',
										src : [ 'slider.js' ],
										expand : true,
										filter : 'isFile',
										dest : '<%= project.build %>/js/angular-bootstrap-slider'
									}, ]
						},
					},
					
					// Concatenate unminified scripts
					concat : {
						options : {
							separator : ';'
						},
						bootstrap : {
							src : [
									'<%= project.build %>/js/bootstrap-slider/bootstrap-slider.min.js',
									'<%= project.build %>/js/bootstrap/*.js' ],
									dest : '<%= project.dist %>/js/bootstrap.bundle.js'
						},
						angular : {
							src : [ '<%= project.build %>/js/angular-bootstrap-slider/slider.js' ],
							dest : '<%= project.dist %>/js/angular.extras.js'
						}
					},
					
					// Minify scripts
					uglify : {
						options : {
							mangle : false
						},
						js : {
							files : [ {
								expand : true,
								cwd : '<%= project.dist %>/js/',
								src : [ '{,*/}*.js' ],
								dest : '<%= project.public %>/js/',
								ext : '.min.js',
								extDot : 'last'
							} ]
						}
					},
					
					jshint : {
						// define the files to lint
						files : [ 'Gruntfile.js',
								'<%= project.dist %>/js/{,*/}*.js',
								'<%= project.dist %>/js/{,*/}*.js' ],
						options : {
							// more options here if you want to override JSHint defaults
							globals : {
								jQuery : true,
								console : true,
								module : true
							}
						}
					},
					
					sass : {
						stage : {
							options : {
								style : 'expanded',
								compass : true
							},
							files : {
								'<%= project.public %>/css/style.css' : '<%= project.scss %>'
							}
						}
					},
					
					// Clean Folders
					clean : [ "<%= project.dist %>", "<%= project.build %>" ],
					
					watch : {
						grunt : {
							files : [ 'Gruntfile.js' ]
						},
						sass : {
							files : '<%= project.assets %>/sass/{,*/}*.{scss,sass}',
							tasks : [ 'sass:stage' ]
						},
						minify : {
							files : '<%= project.dist %>/js/{,*/}*.js',
							tasks : [ 'uglify:js' ]
						},
						merge : {
							files : '<%= project.build %>/**',
							tasks : [ 'concat:raw', 'concat:dist',
									'concat:fonts' ]
						},
						jshint : {
							files : '<%= jshint.files %>',
							tasks : [ 'jshint' ]
						}
					}
				});
		grunt.loadNpmTasks('grunt-contrib-uglify');
		grunt.loadNpmTasks('grunt-contrib-jshint');
		grunt.loadNpmTasks('grunt-contrib-concat');
		grunt.loadNpmTasks('grunt-contrib-sass');
		grunt.loadNpmTasks('grunt-contrib-watch');
		grunt.loadNpmTasks('grunt-contrib-copy');
		grunt.loadNpmTasks('grunt-contrib-clean');
		
		grunt.registerTask('update', [ 'delete', 'jshint', 'sass', 'copy' ]);
		grunt.registerTask('build', [ 'delete', 'jshint', 'sass', 'copy',
				'concat', 'uglify' ]);
		grunt.registerTask('delete', [ 'clean' ]);
		grunt.registerTask('default', [ 'update', 'watch' ]);
	};
}());
