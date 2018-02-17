var allAnimations  = 'animated pulse bounce fadeIn fadeOut';
var savedAnimation = 'pulse';
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
    window.onbeforeunload = function() {
        var hasUnsaved = $('.field-card.edited').length > 0;
        if (hasUnsaved) {
            return "You have unsaved changes on this dashboard. Do you want to leave this page and discard your changes or stay on this page?";
        }
    };
});
jQuery.expr[':'].icontains = function(a, i, m) {
    return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
};
$('.filter-input').keyup( function () {
    $('.filter-card').removeClass(allAnimations).addClass('animated pulse');
    $('.filter-col').hide().show();
    var filter = $(this).val();
    $('.filter-card').find(".card-title:not(:icontains(" + filter + "))").parents('.filter-col').hide();
});
function toogleSaveAllButton() {
    if ($('.field-card.edited').length == 0) {
        $('.save-all-button').removeClass('d-none ' + allAnimations).addClass('animated fadeOut');
    } else {
        $('.save-all-button').removeClass('d-none ' + allAnimations).addClass('animated fadeIn');
    }
}
var inputChangeCallback = function () {
    var obj = $(this);
    var card = obj.parents('.field-card');
    var wasEdited;
    if (obj.prop('type') == 'checkbox') {
        wasEdited = obj.prop('checked').toString() != obj.data('value').toString();
        obj.data('new-value', obj.prop('checked').toString());
    } else {
        wasEdited = obj.val() != obj.data('value');
        obj.data('new-value', obj.val());
    }
    if (wasEdited) {
        card.addClass('edited');
    } else {
        card.removeClass('edited');
    }
    card.removeClass(allAnimations);
    toogleSaveAllButton();
};
$('.field-input-text').keyup(inputChangeCallback);
$('.field-input-checkbox').change(inputChangeCallback);
$('.field-input-select').change(inputChangeCallback);
$('.field-card :checkbox').change(function() {
    var obj = $(this);
    var value = this.checked.toString();
    $('#' + obj.prop('id') + '-value').val(value);
});
$('.save-all-button').click(function(e) {
    e.preventDefault();
    var urlParameters = $('.field-card.edited').find('form').serialize();
    $.post(document.location, urlParameters, function (data) {
        if (true !== data) {
            showAlert('Failed to store new values in Consul key-value storage.', 'error');

            return;
        }
        $('.form-control[data-value]').each(function(_, input) {
            input = $(input);
            var newValue = input.data('new-value');
            if (typeof newValue == "undefined") {

                return;
            }
            input.data('value', newValue.toString());
        });
        $('.field-card.edited')
            .removeClass('edited')
            .addClass('animated ' + savedAnimation);
        toogleSaveAllButton();
        //showAlert('Stored new values in Consul key-value storage.', 'success');
    });
});
var alertTimeout;
function showAlert(message, severity) {
    severity = severity || 'notice';
    var color;
    switch (severity) {
        case 'notice':  color = 'grey'; break;
        case 'error':   color = 'danger-color'; break;
        case 'warning': color = 'warning-color'; break;
        case 'success': color = 'success-color'; break;
    }
    severity = severity.charAt(0).toUpperCase() + severity.slice(1);
    window.clearTimeout(alertTimeout);
    $('.alert p span').html(message);
    $('.alert-severity').text(severity + ":");
    $('.alert')
        .removeClass('grey danger-color warning-color success-color')
        .addClass(color)
        .removeClass('d-none animated fadeIn fadeOut')
        .addClass('animated fadeIn');
    alertTimeout = window.setTimeout(function() {
        $('.alert')
            .removeClass('animated fadeIn fadeOut')
            .addClass('animated fadeOut');
    }, 5000);
}