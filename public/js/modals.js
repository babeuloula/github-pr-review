window.modals = function (title, content, options, callback) {
    if (!options.modalId) {
        throw new Error("option.modalId is required.");
    }

    if (content === undefined) {
        content = jQuery("<div/>");
    } else {
        content = jQuery(content);
    }

    let $modal = jQuery("<div/>").addClass('modal fade').attr('role', 'dialog');

    /**
     * @param string modalId (Required)
     * @param bool hideClose Show or not close button
     * @param bool progressBar Show or not a progressbar
     * @param string width modal-wide, modal-lg, modal-sm or empty string
     */
    let o = {
        modalId: 'modal_' + new Date().getTime(),
        hideClose: false,
        progressBar: false,
        progressBarWidth: 0,
        progressBarVal: 0,
        width: '',
        buttons: null
    };

    if (options === {}) {
        options = o;
    } else if (options.buttons === undefined) {
        options.buttons = options;
    }

    options = jQuery.extend(o, options);

    $modal.attr('id', options.modalId);

    let $modal_dialog = jQuery("<div/>").addClass('modal-dialog ' + o.width);

    if (o.width === 'modal-wide') {
        $modal_dialog.width('90%');
    }

    let $modal_content = jQuery("<div/>").addClass('modal-content');

    let $modal_header = "";

    if (title !== null) {
        $modal_header = jQuery("<div/>").addClass('modal-header');

        let $modal_close = '';

        if (!options.hideClose) {
            $modal_close = jQuery("<button/>").attr('type', 'button')
                .addClass('close')
                .attr('data-dismiss', 'modal')
                .html(
                    jQuery('<span/>').attr('aria-hidden','true').html('&times;')
                );
        }

        $modal_header.append(
            $modal_close
        ).append(
            jQuery("<h4/>").addClass('modal-title').html(title)
        )
    }

    let $modal_body = jQuery("<div/>").addClass('modal-body');

    let $modal_progress = jQuery("<div/>").addClass('progress')
        .html(
            jQuery("<div/>").addClass('progress-bar progress-bar-striped active')
                .width(options.progressBarWidth + '%')
                .html(
                    (options.progressBarVal === 'none') ? '' : jQuery("<span/>").html(options.progressBarVal + '%')
                )
        );

    let $modal_footer = "";



    /**
     * @param string label Text on the button
     * @param string id Button ID
     * @param string type Bootstrap class for the button
     * @param string buttonType submit, button or file
     * @param callable fonction Callback trigger after click
     */

    if (options.buttons) {
        $modal_footer = jQuery('<div/>').addClass('modal-footer');

        if (!o.hideClose) {
            jQuery("<button/>").attr('data-dismiss', 'modal')
                .addClass('btn btn-default')
                .html("Fermer")
                .appendTo($modal_footer);
        }

        for (let i = 0; i < options.buttons.length; i++) {
            if (typeof(options.buttons[i]) === "object") {
                let btnType = (options.buttons[i].buttonType !== undefined) ? options.buttons[i].buttonType : 'button';

                let $button = jQuery("<input/>").attr('id', options.buttons[i].id)
                    .attr('type', btnType)
                    .addClass('btn ' + options.buttons[i].type)
                    .val(options.buttons[i].label)
                    .click(options.buttons[i].fonction);

                $modal_footer.append($button);
            }
        }
    }

    $modal.append(
        $modal_dialog.append(
            $modal_content.append(
                $modal_header
            ).append(
                $modal_body.append(content)
            ).append(
                $modal_footer
            )
        )
    );

    if (options.progressBar) {
        content.after($modal_progress);
    }

    jQuery('body').append($modal);

    $modal.modal({
        keyboard: false,
        backdrop: false
    }).on('shown.bs.modal', function (event) {
        console.log(callback);

        if (callback !== undefined) {
            callback(event);
        }
    });
};


jQuery(document).on('hidden.bs.modal', '.modal', function (e) {
    jQuery(this).remove();
});

jQuery(document).on("show.bs.modal", '.modal .modal-wide', function () {
    let height = jQuery(window).height() - 200;
    jQuery(this).find(".modal-body").css("max-height", height);
});

