module.exports = function (grunt) {
  require('load-grunt-tasks')(grunt);
  grunt.initConfig({

    php: {
        dist: {
            options: {
                port: 5002,
                base: '.',
                keepalive: true
            }
        }
    }

  });

  grunt.registerTask('default', ['php']);
};
