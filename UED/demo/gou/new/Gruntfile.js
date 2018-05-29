'use strict';

function mix(r, s, ov){
	if(!s || !r) return r;
	if(ov===undefined) ov = true;

	for(var p in s){
		if(ov || !(p in r)){
			r[p] = s[p];
		}
	}
	return r;
}

// Stylus & noMerge mode (1:0)
module.exports = function(grunt){
	var appRoot = '../../../repos/apps/gou',
		tasks = ['stylus', 'cssmin', 'min'],
		cfg = {
			corePath: '../../../repos/sys/reset/', srcPath: appRoot+'/src',
			cssPath: appRoot+'/assets/css', jsPath: appRoot+'/assets/js'
		};

	grunt.loadNpmTasks('grunt-contrib-stylus');
	grunt.loadNpmTasks('grunt-yui-compressor');
	grunt.loadNpmTasks('grunt-contrib-watch');

	mix(cfg, //任务配置项
	{
		stylus: {
			dist: {
				expand: true,
				cwd: '<%=srcPath%>/',
				src: '*.source.styl',
				cssPath: '<%=cssPath%>/',
				rename: function(dest, matchedSrcPath, options){
					return options.dest = options.cssPath + matchedSrcPath.replace(/styl$/, 'css');
				}
			}
		},

		cssmin: {
			dist: {
				expand: true,
				src: '<%=cssPath%>/*.source.css',
				rename: function(dest, matchedSrcPath, options){
					return options.dest = matchedSrcPath.replace(/\.source/g, '');
				}
			}
		},

		min: {
			dist: {
				expand: true,
				src: ['<%=jsPath%>/**/*.source.js', 'wifi/**/*.source.js'],
				rename: function(dest, matchedSrcPath, options){
					return options.dest = matchedSrcPath.replace(/\.source/g, '');
				}
			}
		},

		watch: {
			options: {
				spawn: false,
				event: ['added', 'changed', 'created']
			},
			style: {
				files: '<%=srcPath%>/*.source.styl',
				tasks: ['stylus', 'cssmin']
			},
			script: {
				files: ['<%=jsPath%>/**/*.source.js', 'wifi/**/*.source.js'],
				tasks: ['min']
			}
		}
	});

	// init & watch
	grunt.initConfig(cfg);
	grunt.event.on('watch', function(action, filepath) {
		if(grunt.file.isMatch(grunt.config('watch.style.files'), filepath)){
			var sfile = filepath.match(/[\w\.]+$/g)[0];
			grunt.config('stylus.dist.src', sfile);
			grunt.config('cssmin.dist.src', cfg.cssPath + '/' + sfile.replace(/styl/, 'css'));
		}
		if(grunt.file.isMatch(grunt.config('watch.script.files'), filepath)){
			grunt.config('min.dist.src', filepath);
		}
	});

	// tasks
	grunt.registerTask('default', tasks.concat('watch'));
}