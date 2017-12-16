jQuery.fn.reverse = Array.prototype.reverse;

POW.forms = {};

$(function() {

    $(document).on('submit', 'form', POW.forms.submit);
    $(document).on('click', '[data-submit]', POW.forms.button_submit);
    $(document).on('submit', 'form[get-html]', form_get_html);
    $(document).on('change', 'form[interactive][get-html] select', form_get_html);
    $(document).on('click', '[data-post]', form_submit_target);
    $(document).on('change', 'input[type=checkbox]', POW.forms.process_hide_show);
    $(document).on('click', 'input[type=checkbox][data-select-all]', POW.forms.check_all);
    $(document).on('change', 'input[type=checkbox][data-checked-show],input[type=checkbox][data-checked-hide],input[type=checkbox][data-not-checked-show],input[type=checkbox][data-not-checked-hide]', POW.forms.process_hide_show);

    //plugin bootstrap minus and plus
    //http://jsfiddle.net/laelitenetwork/puJ6G/
    $('.btn-number').click(function(e){
        e.preventDefault();

        var type      = $(this).data('type');
        var input =  $(this).parent().parent().find('input.controls');
        var max = +input.attr("max");
        var currentVal = parseInt(input.val());

        if (!isNaN(currentVal)) {
            if (type == 'minus') {
                if(currentVal>0)
                    input.val(currentVal - 1);
            }
            else if(type == 'plus') {
                if (!max || currentVal < max)
                    input.val(currentVal + 1);
            }
        } else {
            input.val(0);
        }
    });

    POW.forms.process_hide_show();
    update_multiselect('form.new select[multiple]', 175);
    update_multiselect('form select[multiple]');

    $(document).on('modal-shown', function(e, id) {
        var context = id ? $('#' + id) : null;

        POW.forms.process_hide_show(null, context);
        update_multiselect('form select[multiple]', null, context);
    });

    $(document).on('change', 'select:not([multiple])', function() {
        var select = $(this),
            option = $('option:selected', select);

        if (option.data('show')) {
            $(option.data('show')).show();
        }
        if (option.data('hide')) {
            $(option.data('hide')).hide();
        }
    });
});

function submit_filter()
{
    $('form.filter').submit();
}

POW.forms.process_hide_show = function(e, context)
{
    $('select [data-show]:selected', context).each(function() {
        $($(this).data('show')).show();
    });
    $('select [data-hide]:selected', context).each(function() {
        $($(this).data('hide')).hide();
    });
    $('input[type=checkbox][data-checked-show]:checked', context).each(function() {
        $($(this).data('checked-show')).show();
    })
    $('input[type=checkbox][data-checked-hide]:checked', context).each(function() {
        $($(this).data('checked-hide')).hide();
    })
    $('input[type=checkbox][data-not-checked-show]:not(:checked)', context).each(function() {
        $($(this).data('not-checked-show')).show();
    })
    $('input[type=checkbox][data-not-checked-hide]:not(:checked)', context).each(function() {
        $($(this).data('not-checked-hide')).hide();
    })
}

function update_multiselect(selector, width, context)
{
    $(selector, context)
        .multiselect({
            maxHeight: 300,
            numberDisplayed: 0,
            buttonWidth: width ? width : null,
            includeSelectAllOption: true,
            enableFilter: true,
        })
        .multiselect('refresh');
}

POW.forms.button_submit = function(e)
{
    e.preventDefault();

    var button = $(this);

    // disable buttons only when allowed to submit
    if (POW.forms.submit.apply(this, [e]))
    {
        button.prop('disabled', true);

        if (button.is('input[type=submit]')) {
            button.val('Processing...');
        }
        else if (button.is('button')) {
            button.text('Processing...');
        }
    }
}

POW.forms.submit = function(e, data)
{
    var form = $(this).closest('form');
    var allow = true;

    form.find('input[required]:not([disabled]):not([type=checkbox]):visible,select[required]:not([disabled]):visible').each(function() {
        var el = $(this);
        if (el.val()) {
            el.closest('.form-group').removeClass('has-error');
        }
        else {
            el.closest('.form-group').addClass('has-error');
            allow = false;
        }
    });

    form.find('input[type=checkbox]:not([disabled]):visible').each(function() {
        var el = $(this),
            selector = 'input[name=%s]'.replace('%s', el.attr('name').replace(/[\[]/g, '\\[').replace(/[\]]/g, '\\]')),
            object = $(selector+':checked');

        if (object.val()) {
            object.closest('.form-group').removeClass('has-error');
        }
        else {
            $(selector).closest('.form-group').addClass('has-error');
            allow = false;
        }
    });

    if (!allow) {
        e.preventDefault();
        bootstrap_alert('danger', 'Please fill out required fields');
        return false;
    }

    if (form.attr('enctype'))
    {
        form.find('input[type=submit].reactive')
            .val('Processing...')
            .prop('disabled', true);
    }
    else if (form.is('[ajax]')) {
        e.preventDefault();
        POW.msg.rc(form.attr('method'), form.attr('action'), data ? data : form.serialize());
    }

    return true;
}

POW.forms.check_all = function(){
    var context = $(this).closest('table,form');

    if($(this).parents().eq(3).hasClass('fixedHeader-floating'))
        context = context.add($('table.dataTable'));//used when dataTable fixedHeader is enabled

    $('input[type=checkbox]',context).prop('checked', $(this).prop('checked'));
}

function form_get_html(e)
{
    var el = $(this);
    var form = el.closest('form');

    if (form.is(el)) {
        e.preventDefault();
    }

    POW.msg.rc(form.attr('method'), form.attr('action'), form.serialize(), function(data) {
        var selector = form.data('target');
        if (selector) {
            var target = $(selector);
            target.html(data);

            $(document).trigger('html-update');
        }
    });
}

function form_submit_target(e, callback)
{
    var el = $(this);
    var target = el.data('post') ? $(el.data('post')) : $(el.data('target'));
    var html = el.html();

    e.preventDefault();

    el.prop('disabled', true);
    if (el.is(':not(input[type=submit])')) {
        el.html('<i class="fa fa-spin fa-cog"></i>&nbsp;Processing...');
    }

    $.ajax({
        url: el.data('url') ? el.data('url') : target.attr('action'),
        method: target.attr('method') ? target.attr('method') : 'post',
        data: target.serialize(),
        success: function(data) {
            el.prop('disabled', false);
            if (el.is(':not(input[type=submit])'))
                el.html(html);

            if (callback) {
                callback.call(el, data);
            }
        }
    });
}

function rewrite_filter_select(selector, html)
{
    var select = $(selector),
        parent = select.parent(),
        select_clone;

    select_clone = $(select.get(0).cloneNode())
        .insertBefore(parent)
        .html(html)
        .prop('disabled', html.length ? false : true);
    
    parent
        .remove();

    if (select.is('[multiple]')) {
        update_multiselect(selector);
    }
}
