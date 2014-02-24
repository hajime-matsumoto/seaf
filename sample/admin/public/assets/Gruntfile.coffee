# Grunt File

module.exports = ->
  pkg = grunt.file.readJSON('package.json')
  grunt.initConfig
    compass:
      dist:
        options:
          config: 'config.ru'
    watch:
      sass:
        files:['sass/style.scss']
        tasks:['compass','cmq','csscomb']
    cmq:
      dev:
        files:
          'css/': ['css/style.css']
    csscomb:
      dev:
        expand: true
        cwd:'css/'
        src:['*.css']
        dest: 'css/'

  for taskName in pkg.devDependencies
    if taskName.substring 0,6 == 'grunt-'
      grunt.loadNpmTasks taskName

  grunt.registerTask 'default',->
    grunt.warn = grunt.fail.warn = (warning)->
      grunt.log.error warning
