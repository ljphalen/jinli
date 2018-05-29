'use strict';

module.exports = function(grunt){
  // compress
  grunt.registerMultiTask('concatMin', 'YUI-Compressor', function(){
    if(/css/i.test(this.target)){
      grunt.config('cssmin', this.files);
      grunt.task.run('cssmin');
    }
    else {
      grunt.config('min', this.files);
      grunt.task.run('min');
    }
  });

  // singleCompress
  grunt.registerMultiTask('singleMin', 'YUI Compressor for the single file', function(){
    var ofs = {};
    this.files.forEach(function(file) {
        file.src.forEach(function(f){
          if(f.indexOf('*')<0)
            ofs[f.split('.source').join('')] = f;
        });
    });
    if(/css/i.test(this.target)){
      grunt.config('cssmin', ofs);
      grunt.task.run('cssmin');
    }
    else {
      grunt.config('min', ofs);
      grunt.task.run('min');
    }
  });

  // main
  var tasks = ['stylus', 'singleMin:css', 'singleMin:js'];
  grunt.initConfig({
    cssPath: 'assets/css',
    jsPath: 'assets/js',

    stylus: {
      dist: {
        src: ['<%=cssPath%>/main.source.styl'],
        dest: '<%=cssPath%>/main.source.css'
      }
    },

    singleMin: {
      css: {
        src: ['<%=cssPath%>/*.source.css']
      },
      js: {
        src: ['<%=jsPath%>/**/*.source.js']
      }
    },

    watch: {
      script: {
        files: ['<%=jsPath%>/**/*.source.js'],
        tasks: ['singleMin:js']
      },

      stylus: {
        files: ['<%=cssPath%>/main.source.styl'],
        tasks: ['stylus', 'singleMin:css']
      }
    }
  });

  // tasks
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-stylus');
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-yui-compressor');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.registerTask('default', tasks.concat('watch'));
};