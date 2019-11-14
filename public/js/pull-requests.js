jQuery(function ($) {
    var interval = null;
    var intervalNextReload = null;
    var nextReload = null;
    var favicon = new Favico({
        animation:'none'
    });
    var listener = new window.keypress.Listener();

    /** Start an AJAX request to get new Pull requests ans Notifications */
    window.reloadData = function () {
        let $accordionPullRequests = $(document).find('#accordion-pull-requests');
        let $accordionNotifs = $(document).find('#accordion-notifs');

        $.ajax({
            url: $accordionPullRequests.attr('data-url'),
            success: function(response, textStatus, jqXHR) {
                $accordionPullRequests.html(response);
                hideCollapses();
                updateFavicon();
            },
            error: function(response, textStatus, errorThrown) {
                alert("Unable to load pull requests.");
            }
        });

        $.ajax({
            url: $accordionNotifs.attr('data-url'),
            success: function (response, textStatus, jqXHR) {
                $accordionNotifs.html(response);
                hideCollapses();
                updateFavicon();
            },
            error: function (response, textStatus, errorThrown) {
                alert("Unable to load notifications.");
            }
        });

        updateNextReload();
    };

    /** Save collapses on localStorage */
    window.saveCollapses = function (collapses) {
        if (undefined === collapses) {
            throw new Error("Missing parameter 'collapses'");
        }

        localStorage.setItem(LOCALSTORAGE_KEY, JSON.stringify(collapses));
    };

    /** Get collapses from localStorage */
    window.getCollapses = function () {
        let collapses = JSON.parse(localStorage.getItem(LOCALSTORAGE_KEY));

        return null === collapses ? {} : collapses;
    };

    /** Hide closed collapses after AJAX request */
    window.hideCollapses = function () {
        $.each(getCollapses(), function (id, display) {
            if (false === display) {
                $('#'+id).removeClass('show');
            }
        });
    };

    /** Update favicon badge */
    window.updateFavicon = function () {
        let count = 0;

        $(document).find('.accordion .collapse').each(function (index, elem) {
            count = count + parseInt($(elem).attr('data-count'), 10);
        });

        if (0 < count) {
            favicon.badge(count);
        }
    };

    /** Init Keypress for reload page */
    window. initKeypress = function() {
        listener.simple_combo("ctrl r", function (event) {
            event.preventDefault();
            event.stopPropagation();

            reloadData();
        });
        listener.simple_combo("ctrl f5", function (event) {
            event.preventDefault();
            event.stopPropagation();

            reloadData();
        });
        listener.simple_combo("f5", function (event) {
            event.preventDefault();
            event.stopPropagation();

            reloadData();
        });
    };

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

        interval = window.setInterval(reloadData, RELOAD_EVERY);
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

    if (true === RELOAD_ON_FOCUS) {
        $(window).on('focus', reloadData);
    } else if(RELOAD_EVERY > 0) {
        initInterval();
    }

    $(document).ready(function () {
        let collapses = getCollapses();

        $(document).find('.accordion .collapse').each(function (index, elem) {
            let id = $(elem).attr('id');

            if (undefined === collapses[id]) {
                collapses[id] = true;
            } else if (false === collapses[id]) {
                $(elem).removeClass('show');
            }
        });

        saveCollapses(collapses);
        updateFavicon();
        initKeypress();
        updateNextReload();
    });

    $(document).on('click', '.accordion .card-header', function (event) {
        event.preventDefault();
        event.stopPropagation();

        let collapses = getCollapses();
        let $collapse = $(this).next();
        let id = $collapse.attr('id');

        if ($collapse.hasClass('show')) {
            $collapse.slideUp(250, function () {
                $(this).removeClass('show');
            });

            collapses[id] = false;
        } else {
            $collapse.slideDown(250, function () {
                $(this).addClass('show');
            });

            collapses[id] = true;
        }

        saveCollapses(collapses);
    });

    $(document).on('click', '.notification-mark-as-read', function (event) {
        event.preventDefault();
        event.stopPropagation();

        let $that = $(this);

        $that.find('i').removeClass('text-muted').addClass('text-success');

        $.ajax({
            url: $that.attr('data-url'),
            type: 'POST',
            success: function(response, textStatus, jqXHR) {
                let $collapse = $($that.parents('.collapse').get(0));
                let $strong = $collapse.prev().find('strong');
                let number = $strong.text()
                    .replace('(', '')
                    .replace(')', '')
                ;
                number = parseInt(number, 10);

                let $listItem = $($that.parents('.list-group-item').get(0));

                let nextTag = $listItem.next()[0] === undefined ? undefined : $listItem.next()[0].tagName.toLowerCase();
                let prevTag = $listItem.prev()[0] === undefined ? undefined : $listItem.prev()[0].tagName.toLowerCase();

                if (('div' === nextTag && 'div' === prevTag)
                    || (undefined === nextTag && 'div' === prevTag)
                ) {
                    $($listItem.prev().get(0)).slideUp(250, function () {
                        $(this).remove();
                    });
                }

                $listItem.slideUp(250, function () {
                    --number;
                    $strong.text('('+number+')');
                    $collapse.attr('data-count', number);

                    if (0 === number) {
                        $collapse.slideUp(250, function () {
                            $(this).removeClass('show');
                        });
                    }

                    $(this).remove();
                    updateFavicon();
                });
            },
            error: function(response, textStatus, errorThrown) {
                alert("Unable to mark as read notification.");
            }
        });
    });
});
