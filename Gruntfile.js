/**
 * Created by rflach on 10/3/15.
 */
'use strict';

module.exports = function (grunt) {
  // load all grunt tasks
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-postcss');
  grunt.loadNpmTasks('grunt-shell');

  grunt.initConfig({
    watch: {
      // if any .less file changes in directory "less/" run the "less"-task.
      files: "less/*.less",
      tasks: ["less","postcss","shell"]
    },
    shell: {
        options: {
            stderr: false
        },
        target: {
            command: "find node_modules/ -name '*.info' -type f -delete"
        }
    },
    // "less"-task configuration
    less: {
      // production config is also available
      development: {
        options: {
          // Specifies directories to scan for @import directives when parsing.
          // Default value is the directory of the source, which is probably what you want.
          paths: ["less","node_modules/bootstrap/less"],
        },
        files: {
          // compilation.css  :  source.less
          "css/style.css": "less/style.less"
        }
      },
    },
    postcss: {
      options: {
        map: true,
        processors: [
          require('autoprefixer')({
            browsers: ['> 2%']
          })
        ]
      },
      dist: {
        src: 'css/*.css'
      }
    }
  });
  // the default task (running "grunt" in console) is "watch"
  grunt.registerTask('default', ['watch']);
  grunt.registerTask('build', ['less','postcss','shell']);
};
