/* local path 

cd "m:\plugin\mashshare-networks\trunk\mashshare-networks"
 * 
 * // path to remote file
wp-content/uploads/edd/2014/10/mashshare-networks.zip
 * 
 */
module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
                
        pkg: grunt.file.readJSON( 'package.json' ),
        paths : {
            // Base destination dir
            base : '../../tags/<%= pkg.version %>/<%= pkg.name %>/',
            zipbase: '../../tags/<%= pkg.version %>' 
        },

        // Tasks here
        // Bump version numbers
        version: {
            css: {
                options: {
                    prefix: 'Version\\:\\s'
                },
                src: [ 'style.css' ]
            },
            php: {
                options: {
                        prefix: '\@version\\s+'
                },
                src: [ 'functions.php', '<%= pkg.name %>.php' ]
            }
        },
        // minify js
        uglify: {
            build: { 
                files:[
                    {
                    //'assets/js/mashnet.min.js' : 'assets/js/mashnet.js'
                    '<%= paths.base %>/assets/js/mashnet.min.js' : 'assets/js/mashnet.js'
                    }
                ]
            }
        },
        // Minify CSS files into NAME-OF-FILE.min.css
        cssmin: {
            build: { 
                files:[
                    //{'assets/css/mashnet.min.css' : 'assets/css/mashnet.css'}
                    {'<%= paths.base %>/assets/css/mashnet.min.css' : 'assets/css/mashnet.css'}
                ]
            }
        },
        // Copy to build folder
        copy: {
            build: {
                src: ['**', '!node_modules/**', '!Gruntfile.js', '!package.json', '!nbproject/**', '!grunt/**'],
                dest: '<%= paths.base %>'
            }
        },
        'string-replace': {
                build: {
                    files: {
                        //'<%= paths.basetrunk %>/mashshare-networks.php' : 'mashshare-networks.php',
                        '<%= paths.base %>/mashshare-networks.php' : 'mashshare-networks.php',
                        '<%= paths.base %>/readme.txt': 'readme.txt'
                        //'<%= paths.basetrunk %>readme.txt': 'readme.txt'
                    },
                    options: {
                        replacements: [{
                                pattern: /define\('MASHSB_DEBUG', true\);/g,
                                replacement: 'define(\'MASHSB_DEBUG\', false);'
                            },{
                                pattern: /{{ version }}/g,
                                replacement: '<%= pkg.version %>'
                        }]
                    }
                }
            },
        // Clean the build folder
        clean: {
            options: { 
                force: true 
            },
            build: {
                src: ['<%= paths.base %>']
            }
        },
        
        // Compress the build folder into an upload-ready zip file
        compress: {
            build: {
                options: {
                    archive: '<%= paths.zipbase %>/<%= pkg.name %>.zip'
                },
                cwd: '<%= paths.base %>',
                src: ['**/*'],
                dest: '<%= pkg.name %>/',
                expand:true
            }
        }


    });

    // Load all grunt plugins here
    // [...]
    //require('load-grunt-config')(grunt);
    require('load-grunt-tasks')(grunt);
    
    // Display task timing
    require('time-grunt')(grunt);

    // Build task
    grunt.registerTask( 'build', [ 'clean:build', 'copy:build', 'uglify:build', 'string-replace:build', 'cssmin:build', 'compress:build' ]);


};