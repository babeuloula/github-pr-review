jQuery(function ($) {
    var interval = null;
    var intervalNextReload = null;
    var nextReload = null;

    /** Update next reload at span */
    window.updateNextReload = function () {
        if (false === RELOAD_ON_FOCUS && 0 < RELOAD_EVERY) {
            nextReload = moment().add(RELOAD_EVERY, 'milliseconds');

            initIntervalNextReload();
            initInterval();
        }
    };

    /** Clear reload interval */
    window.initInterval = function () {
        clearInterval(interval);

        if (typeof reloadData === 'function') {
            interval = window.setInterval(reloadData, RELOAD_EVERY);
        }
    };

    /** Clear next reload interval */
    window.initIntervalNextReload = function () {
        clearInterval(intervalNextReload);

        intervalNextReload = window.setInterval(
            function () {
                $(document).find('#next-reload').text(
                    parseInt(nextReload.format('X'), 10) - parseInt(moment().format('X'), 10)
                );
            },
            1000
        );
    };
    updateNextReload();
});
