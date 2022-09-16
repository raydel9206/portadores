"use strict";

var gulp = require('gulp'),
    chalk = require('chalk'),
    concatjs = require('gulp-concat'),
    concatcss = require('gulp-concat-css'),
    uglifyjs = require('gulp-uglify-es').default,
    uglifycss = require('gulp-uglifycss'),
    rename = require('gulp-rename'),
    gzip = require('gulp-gzip'),
    merge = require('merge-stream'),
    printf = require('printf'),
    clitable = require('cli-table2'),
    table = new clitable({head: ["", "Sources", "Output"]});

module.exports = {
    table: table,
    libs: {
        jquery_js: 'jquery@3.2.1/jquery.js',
        bootstrap_js: 'bootstrap@4.1.1/js/bootstrap.bundle.js', // includes popper.js
        bootstrap_css: 'bootstrap@4.1.1/css/bootstrap.css',
        idle_timer_js: 'idle-timer@1.1.0/idle-timer.js',
        fontawesome5_css: 'fontawesome-free@5.4.1/css/all.css',
        // fontawesome5_css: 'fontawesome-free@5.0.13/web-fonts-with-css/css/fontawesome-all.css',
        flipclock_js: 'flipclock@0.7.8/flipclock.js',
        flipclock_css: 'flipclock@0.7.8/flipclock.css'
    },
    process: function (vars, compressed) {
        return function () {
            var src_js = vars.prefix + (vars.suffix || '') + '.js',
                dest_js = vars.prefix + (vars.suffix || '') + '.min.js',
                src_css = vars.prefix + (vars.suffix || '') + '.css',
                dest_css = vars.prefix + (vars.suffix || '') + '.min.css';

            return merge(
                gulp
                    .src(vars.scripts)
                    .pipe(concatjs(src_js))
                    .pipe(gulp.dest(vars.dest))
                    .pipe(rename(dest_js))
                    .pipe(uglifyjs())
                    .pipe(gulp.dest(vars.dest))
                    .on('end', function () {
                        table.push({'JS': [chalk.yellow(vars.scripts.join('\n')), chalk.yellow(vars.dest + dest_js) + chalk.red(compressed ? ' (.gz)' : '')]});
                    }),
                compressed ?
                    gulp
                        .src(vars.scripts)
                        .pipe(concatjs(src_js))
                        .pipe(gzip())
                        .pipe(gulp.dest(vars.dest))
                        .on('end', function () {
                            //  table.push({'JS': [chalk.yellow(vars.dest + dest_js), chalk.yellow(vars.dest + dest_js + '.gz')]});
                        }) :
                    [],
                compressed ?
                    gulp
                        .src(vars.scripts)
                        .pipe(concatjs(src_js))
                        .pipe(uglifyjs())
                        .pipe(rename(dest_js))
                        .pipe(gzip())
                        .pipe(gulp.dest(vars.dest))
                        .on('end', function () {
                            // table.push({'JS': [chalk.yellow(dest_js), chalk.yellow(vars.dest + dest_js + '.gz')]});
                        }) :
                    [],
                //------------------------------------------------------------------------------------------------------
                gulp
                    .src(vars.styles, {base: 'assets/vendor/'})
                    .pipe(concatcss(src_css))
                    .pipe(gulp.dest(vars.dest))
                    .pipe(rename(dest_css))
                    .pipe(uglifycss())
                    .pipe(gulp.dest(vars.dest))
                    .on('end', function () {
                        table.push({'CSS': [chalk.yellow(vars.styles.join('\n')), chalk.yellow(vars.dest + dest_css) + chalk.red(compressed ? ' (.gz)' : '')]});
                    }),
                compressed ?
                    gulp
                        .src(vars.styles, {base: 'assets/vendor/'})
                        .pipe(concatcss(src_css))
                        .pipe(gzip())
                        .pipe(gulp.dest(vars.dest))
                        .on('end', function () {
                            // table.push({'CSS': [chalk.yellow(vars.dest + dest_css), chalk.yellow(vars.dest + dest_css + '.gz')]});
                        }) :
                    [],
                compressed ?
                    gulp
                        .src(vars.styles, {base: 'assets/vendor/'})
                        .pipe(concatcss(src_css))
                        .pipe(uglifycss())
                        .pipe(rename(dest_css))
                        .pipe(gzip())
                        .pipe(gulp.dest(vars.dest))
                        .on('end', function () {
                            // table.push({'JS': [chalk.yellow(dest_css), chalk.yellow(vars.dest + dest_css + '.gz')]});
                        }) :
                    []
            );
        }
    }
};