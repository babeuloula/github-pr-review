jQuery(function ($) {
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        let newRepo = $.trim($(e.target).text());
        let oldRepo = $.trim($(e.relatedTarget).text());

        let $title = $('head').find('title');

        let title = $.trim($title.text());
        title = title.replace(oldRepo, newRepo);

        $title.text(title);
    });

    window.showWaitModal = function () {
        $(document).find('#reload-img').fadeIn(250, function () {
            location.reload();
        });
    };

    if (RELOAD_ON_FOCUS) {
        $(window).on('focus', showWaitModal);
    } else if(RELOAD_EVERY > 0) {
        window.setInterval(showWaitModal, RELOAD_EVERY);
    }
});
