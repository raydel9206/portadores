function hideAlert() {
    $(".alert").fadeOut(150, function () {
        $(this).remove();
    });
}

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();

    $('input').focusout(function () {
        $(this).tooltip('hide');
    }).keypress(function () {
        hideAlert();
    });

    $("[type=submit]").click(function (event) {
        hideAlert();
    });

    $('form').submit(function (event) {
        $('[data-toggle="tooltip"]').tooltip('hide');

        event.preventDefault(); // to stop the form from submitting

        // Check if empty of not
        if ($.trim($('[name=_username]').val()) === '' || $.trim($('[name=_password]').val()) === '') {
            return;
        }

        $('button[type=submit]')
            .attr('disabled', 'disabled')
            // .removeClass('btn-outline-secondary').addClass('btn-secondary')
            .addClass('disabled ')
            .children('i').removeClass('fa-sign-in-alt').addClass('fa-circle-notch fa-spin text-dark');

        this.submit(); // If all the validations succeeded
    });

    setTimeout(function () {
        hideAlert();
    }, 5000); // wait 5 seconds and then remove the alert
});