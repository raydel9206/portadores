"use strict";

//----------------------------------------------------------------------------------------------------------------------
// vars
//----------------------------------------------------------------------------------------------------------------------
var gulp = require('gulp'),
    utils = require('./utils'),
    yaml = require('yamljs'),
    vendor_path = '../assets/vendor/',
    output_path = '../build/',
    params = yaml.load('../../src/Geocuba/AdminBundle/Resources/config/services.yml'),
    extjs_path = vendor_path + params['parameters']['extjs_version'] + '/',
    extjs_toolkit = (params['parameters']['extjs_toolkit'] || '.') + '/',
    extjs_theme = params['parameters']['extjs_theme'],
    login_theme = params['parameters']['login_theme'],
    compressed = process.argv.indexOf("--compressed") !== -1;

//----------------------------------------------------------------------------------------------------------------------
// app.js | app.css
// Note: the generated file 'app.js' is not used directly.
//----------------------------------------------------------------------------------------------------------------------
gulp.task('build-app', utils.process({
    prefix: 'app',
    dest: output_path,
    scripts: [
        '../assets/js/app.js'
    ],
    styles: [
        '../assets/css/app.css'
    ]
}, compressed));

//----------------------------------------------------------------------------------------------------------------------
// error.css
//----------------------------------------------------------------------------------------------------------------------
gulp.task('build-error', utils.process({
    prefix: 'error',
    dest: output_path,
    scripts: [],
    styles: [
        vendor_path + utils.libs.bootstrap_css,
        vendor_path + utils.libs.fontawesome5_css,
        '../assets/css/error.css'
    ]
}, compressed));

//----------------------------------------------------------------------------------------------------------------------
// login/vendor.js | login/vendor.css
//----------------------------------------------------------------------------------------------------------------------
gulp.task('build-login-vendor', utils.process({
    prefix: 'vendor',
    dest: output_path + '/login/',
    scripts: [
        vendor_path + utils.libs.jquery_js,
        vendor_path + utils.libs.bootstrap_js
    ],
    styles: [
        vendor_path + utils.libs.bootstrap_css,
        vendor_path + utils.libs.fontawesome5_css
    ]
}, compressed));

//----------------------------------------------------------------------------------------------------------------------
// login/<login_theme>.js | login/<login_theme>.css
//----------------------------------------------------------------------------------------------------------------------
gulp.task('build-login-theme', utils.process({
    prefix: login_theme,
    dest: output_path + '/login/' + login_theme + '/',
    scripts: [
        '../assets/js/login/' + login_theme + '.js'
    ],
    styles: [
        '../assets/css/login/' + login_theme + '.css'
    ]
}, compressed));

//----------------------------------------------------------------------------------------------------------------------
// maintenance.js | maintenance.css
//----------------------------------------------------------------------------------------------------------------------
gulp.task('build-maintenance', utils.process({
    prefix: 'maintenance',
    dest: output_path,
    scripts: [
        vendor_path + utils.libs.jquery_js,
        vendor_path + utils.libs.flipclock_js,
        '../assets/js/maintenance.js'
    ],
    styles: [
        vendor_path + utils.libs.flipclock_css,
        vendor_path + utils.libs.bootstrap_css,
        vendor_path + utils.libs.fontawesome5_css,
        '../assets/css/maintenance.css'
    ]
}, compressed));

//----------------------------------------------------------------------------------------------------------------------
// vendor.js | vendor.css
//----------------------------------------------------------------------------------------------------------------------
gulp.task('build-vendor', utils.process({
    prefix: 'vendor',
    dest: output_path,
    scripts: [
        vendor_path + utils.libs.jquery_js,
        vendor_path + utils.libs.bootstrap_js,
        vendor_path + utils.libs.idle_timer_js,
        extjs_path + 'ext-all-debug.js',
        // extjs_path + 'ext-modern-all-debug.js',
        extjs_path + 'themes/' + extjs_toolkit + extjs_theme + '/' + extjs_theme + '-debug.js',
        extjs_path + 'locale/locale-es-debug.js',
        extjs_path + 'ux/Spotlight.js',
        extjs_path + 'ux/form/TreeComboBoxList.js',
        extjs_path + 'ux/form/TreeComboBox.js',
        extjs_path + 'ux/form/TreePicker.js',
        extjs_path + 'ux/form/MultiSelect.js',
        extjs_path + 'ux/form/SearchField.js',
        extjs_path + 'ux/form/ItemSelector.js'		
    ],
    styles: [
        extjs_path + 'themes/' + extjs_toolkit + extjs_theme + '/resources/' + extjs_theme + '-all-debug.css',
        // extjs_path + 'ux/css/ItemSelector.css',
        vendor_path + utils.libs.bootstrap_css,
        vendor_path + utils.libs.fontawesome5_css
    ]
}, compressed));

//----------------------------------------------------------------------------------------------------------------------
// highcharts.js | highcharts.css
//----------------------------------------------------------------------------------------------------------------------
gulp.task('build-highcharts', utils.process({
    prefix: 'highcharts',
    dest: output_path,
    scripts: [
        vendor_path + 'highcharts@6.0.1/highcharts.src.js',
        vendor_path + 'highcharts@6.0.1/highcharts-3d.src.js',
        vendor_path + 'highcharts@6.0.1/highcharts-more.src.js'
        // vendor_path + 'highcharts@6.0.1/modules/*.src.js',
        // vendor_path + 'highcharts@6.0.1/themes/*.src.js'
    ],
    styles: [
        // vendor_path + utils.libs.fontawesome_css
    ]
}, compressed));

//----------------------------------------------------------------------------------------------------------------------
// main task
//----------------------------------------------------------------------------------------------------------------------
gulp.task('default', ['build-app', 'build-error', 'build-login-vendor', 'build-login-theme', 'build-maintenance', 'build-vendor', 'build-highcharts'], function () {
    console.log(utils.table.toString());
});
//----------------------------------------------------------------------------------------------------------------------
