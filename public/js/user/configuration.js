jQuery(function ($) {
    $(document).on('change', 'input[name="mode"]', function () {
        if ('label' === $(this).val()) {
            $(document).find('#label').show();
            $(document).find('#label').find('select[data-required="true"]').each(function (index, elem) {
                $(elem).attr('required', 'required');
            });
            $(document).find('#filter').hide();
            $(document).find('#filter').find('select[data-required="true"]').each(function (index, elem) {
                $(elem).removeAttr('required');
            });

            initSelect2();
        } else {
            $(document).find('#filter').show();
            $(document).find('#filter').find('select[data-required="true"]').each(function (index, elem) {
                $(elem).attr('required', 'required');
            });
            $(document).find('#label').hide();
            $(document).find('#label').find('select[data-required="true"]').each(function (index, elem) {
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

    $(document).find('input[name="mode"]').trigger('change');

    $(document).on('click', '#delete-account', function (event) {
        event.preventDefault();
        event.stopPropagation();

        let $that = $(this);

        $("body").overhang({
            type: "confirm",
            primary: "#3498DB",
            accent: "#2980B9",
            yesColor: "#2ECC71",
            message: "Are you really sure, you want to delete your account?",
            overlay: true,
            callback: function (value) {
                if (true === value) {
                    $(location).attr('href', $that.attr('data-href'));
                }
            }
        });
    });
});
