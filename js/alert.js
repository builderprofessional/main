function displaySuccess(message)
{
    displayAlert('alert-success', message);
}

function displayAlert(alertClass, message)
{
    var container = $('#resultMessage');

    container.addClass(alertClass);
    container.removeClass('hidden');
    container.html(message);

    setTimeout(function() {
        container.addClass('hidden');
        container.removeClass(alertClass);
        container.html('');
    }, 8000);
}