module.exports = function(grunt) {
    grunt.initConfig({
        concat: {
            options: {
                separator: ';',
                process: function(src, filepath) {
                    return '\n// ============================================================\n// ' + filepath +
                    '\n// ============================================================\n\n' + src;
                },
                banner: '\n\n\n\n\n\n\n\n\n\n/*          This is a generated filed do not edit.          */\n\n\n\n\n\n\n\n\n\n' +
                    '/*          From static/js/ run "grunt concat"          */\n\n\n\n\n\n\n\n\n\n'
            },
            app: {
                src: [
                    'modules/base.js',
                    'modules/pow_message.js',
                    'modules/ajax.js',
                    'modules/forms.js',
                    'modules/modals.js',
                    'modules/tables.js',
                    'modules/monolith.js',
                    'modules/shadow_filter.js',
                    'modules/btn_bucket.js',
                    'modules/swal.presets.js',
                    'apps/*.js',
                    'crap.js'
                ],
                dest: 'app.js'
            }
        },
        watch: {
            js: {
                files: ['apps/*.js', 'modules/*.js'],
                tasks: ['concat']
            }
        }
    });
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default', ['concat']);
};