$(document).ready(function() {
    $('.inlineContactSubmit').click(function() {
        var form = $(this).closest('.modal-content').find('form');
        $(form).ajaxSubmit({url: 'processInlineContact.php', type: 'post'});

        $('#inlineContactModal').modal('hide');
        displaySuccess("Thank you for contacting us. We'll be in touch!");
    });
});