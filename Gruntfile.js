module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    concat: {
      full: {
        files: {
          'dist/alexusMailer_v2.0.php': ['src/init.php', 'lang/*', 'class/*', 'files/*', 'src/process.php', 'src/header.php', 'css/*.min.css', 'src/separator.php', 'src/jquery-*.min.js', 'js/*.min.js', '!js/service.min.js', 'src/footer.php']
        }
      },
      demo: {
        files: {
          'dist/alexusMailer.demo.php': ['src/init.php', 'lang/*', 'class/*', 'files/*', 'src/service.php', 'src/process.php', 'src/header.php', 'css/*.min.css', 'src/separator.php', 'src/jquery-*.min.js', 'js/*.min.js', 'src/footer.php']
        }
      }
    },
    uglify: {
      full: {
        files: {
          'js/scripts.min.js': ['js/*.js', '!js/*.min.js', '!js/service.js']
        }
      }
    },
    cssmin: {
      full: {
        files: {
          'css/styles.min.css': ['css/*.css', '!css/*.min.css']
        }
      }
    },
    copy: {
      main: {
        files: [
          // includes files within path
          {expand: true, src: ['shell/*'], dest: 'dist/', filter: 'isFile'}
        ]
      }
    }
  });

  // Load plugins
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-copy');

  // Default task(s).
  grunt.registerTask('default', ['uglify:full', 'cssmin:full', 'concat:full', 'copy']);
  grunt.registerTask('demo', ['uglify:full', 'cssmin:full', 'concat:demo', 'copy']);
};