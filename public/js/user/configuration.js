jQuery(function ($) {
    $(document).on('change', 'input[name="mode"]', function () {
        if ('label' === $(this).val()) {
            $(document).find('#label').show();
            $(document).find('#label').find('select').each(function (index, elem) {
                $(elem).attr('required', 'required');
            });
            $(document).find('#filter').hide();
            $(document).find('#filter').find('select').each(function (index, elem) {
                $(elem).removeAttr('required');
            });

            initSelect2();
        } else {
            $(document).find('#filter').show();
            $(document).find('#filter').find('select').each(function (index, elem) {
                $(elem).attr('required', 'required');
            });
            $(document).find('#label').hide();
            $(document).find('#label').find('select').each(function (index, elem) {
                $(elem).removeAttr('required');
            });

            initSelect2();
        }
    });

    $(window).resize(function () {
        initSelect2();
    });

    window.initSelect2 = function () {
        try {
            $('select').select2('destroy')
        } catch (e) {}

        $('select').select2({
            allowClear: true
        });
    };
    initSelect2();
});
