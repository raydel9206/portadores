$(document).ready(function () {
    window.App = {
        initialize: function (in_maintenance_until) {
            $.extend(FlipClock.Lang.Spanish, {
                days: 'Días', // Corregir DÍas por Días
                seconds: 'Segundos' // Corregir Segundo por Segundos
            });

            let clock = $('.clock').FlipClock({
                    clockFace: 'DailyCounter', // clockFace: 'YearlyCounter',
                    autoStart: false,
                    language: 'es',
                    // showSeconds: false,
                    callbacks: {
                        stop: function () {
                            location.reload();
                        }
                    }
                }),
                limit_seconds = (99 * 24 * 60 * 60) + (23 * 60 * 60) + (59 * 60) + 59,
                diff_seconds = in_maintenance_until - Math.floor(Date.now() / 1000);

            clock.setCountdown(Math.abs(diff_seconds) < limit_seconds ? diff_seconds > 0 : true);
            clock.setTime(Math.abs(diff_seconds) < limit_seconds ? Math.abs(diff_seconds) : limit_seconds);

            clock.start();
        }
    };
});