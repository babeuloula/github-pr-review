AOS.init();

jQuery(function($){
    let $navBar = $(document).find('#navbarCollapse');

    $navBar.on('click', '.nav-link', function () {
        let id = $(this).attr('href').replace('/', '');
        if (1 === $(document).find(id).length) {
            $('html, body').animate({
                scrollTop: $(id).offset().top
            }, 'slow');
        }

        if ($navBar.hasClass('show')) {
            $(document).find('.navbar-toggler').trigger('click');
        }
    });

    let $backToTop = $('#back-to-top');

    $(window).on('scroll', function () {
        if ($navBar.hasClass('show')) {
            $(document).find('.navbar-toggler').trigger('click');
        }

        if ($(this).height() < $backToTop.offset().top && false === $backToTop.hasClass('display')) {
            $backToTop.addClass('display');
        } else if ($(this).height() > $backToTop.offset().top && true === $backToTop.hasClass('display')) {
            $backToTop.removeClass('display');
        }
    });

    $backToTop.on('click', function (event) {
        event.preventDefault();
        event.stopPropagation();

        $('html, body').animate({
            scrollTop: 0
        }, 'slow');
    });
});
