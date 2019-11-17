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
});
