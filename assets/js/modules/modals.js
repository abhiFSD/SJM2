POW.modals = {};

$(function() {
    $(document).on('click', '[data-modal]:not(select)', POW.modals.click_show);
    $(document).on('click', '[data-modal-close]', POW.modals.button_close);
    $(document).on('change', 'select[data-modal]', POW.modals.select_show);
});

POW.modals.cached = [];
POW.modals.container = '<div class="modal fade" id="{{id}}" role="dialog">';

POW.modals.button_close = function(e) {
    $(this).closest('.modal').find('.close').click();
}

POW.modals.show = function(id, url) {
    if (id && POW.modals.cached[id]) {
        return $('#'+id)
            .html(POW.modals.cached[id])
            .modal('show');
    }

    POW.msg.rc('get', (url.indexOf('http') > -1 ? url : appPath + url), null, function(html) {
        if (id) {
            POW.modals.cached[id] = html;
        }

        var handle = $(POW.modals.container.replace('{{id}}', id))
            .html(html)
            .appendTo('body')
            .modal('show')
            .on('shown.bs.modal', function() { 
                $(document).trigger('modal-shown', [id]); 
            })
            .on('hidden.bs.modal', function() {
                if (!id) handle.remove();
            })
    });
}

POW.modals.click_show = function(e) {
    e.preventDefault();

    var handle = $(this),
        id = handle.data('modal'),
        url = handle.data('url') ? handle.data('url') : handle.attr('href');

    POW.modals.show(id, url);
}

POW.modals.select_show = function(e) {
    e.preventDefault();

    var handle = $(this),
        first = handle.find(':selected').first(),
        id = first.data('id'),
        url = first.data('url');

    if (!id && !url) return;

    handle
        .find('option:nth-child(1)')
        .prop('selected', true);

    POW.modals.show(id, url);
}
