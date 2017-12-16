POW.ajax = {};

$(function() {
    $(document).on('click', '[data-ajax]', POW.ajax.run_data_ajax);
    $(document).on('change', 'select[data-ajax]', POW.ajax.run_select_data_ajax);
});

POW.ajax.run_data_ajax = function(e) {
    var el = $(this),
        url = -1 < el.data('ajax').indexOf('http') ? el.data('ajax') : appPath + el.data('ajax');

    if ($(e.target).is('a')) {
        e.preventDefault();
    }

    if (el.is('select')) return;

    POW.msg.rc('get', url);
}

POW.ajax.run_select_data_ajax = function() {
    var el = $(this),
        url = -1 < el.data('ajax').indexOf('http') ? el.data('ajax') : appPath + el.data('ajax');


    POW.msg.rc('get', url + '/' + el.val());
}
