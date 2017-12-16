$(function () {
    $('div.alert').each(function() {
        var div = $(this),
            match = div.attr('class').match(/(\W|^)alert-([a-z]+)/),
            type = match ? match[2] : 'success';
        
        div.find('[data-dismiss]').remove();

        if (div.text()) {
            bootstrap_alert(type, div.text());
            div.remove();
        }
    })
})

function enable(selector) {
    $(selector).prop('disabled', false);
}

function bootstrap_alert(type, html) {
    var options = {
        message: html
    };
    var settings = {
        animate: {
            enter: 'animated bounceInDown',
        },
        placement: {
            from: 'top',
            align: 'center'
        },
        type: type,
        z_index: 1100
    };

    if (type == 'danger') {
        options.icon = 'glyphicon glyphicon-exclamation-sign';
    }
    else if (type == 'success') {
        options.icon = 'glyphicon glyphicon-ok-circle';
    }
    else if (type == 'warning') {
        options.icon = 'glyphicon glyphicon-warning-sign';
    }

    $.notify(options, settings);
}

function show_backdrop() {
    $('body').addClass('modal-open').append('<div class="widget-box-layer" style="width:100%"><div class="loader"><i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i></div></div>');
}

function remove_backdrop() {
    $('.widget-box-layer').remove();
    $('body').removeClass('modal-open');
}
