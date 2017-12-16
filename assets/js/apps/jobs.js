POW.jobs = {};

$(function() {
    if (!$('#current-jobs').is('body')) return;

    $(document).on('html-update', POW.jobs.prepend_button);
    $(document).on('click', '.bulk-actions .dropdown-menu li:not(.transfer-job)', POW.jobs.submit_jobs);
    $(document).on('click', '.bulk-actions .dropdown-menu li.transfer-job', POW.jobs.open_transfer_job);
    $(document).on('keydown', '#transfer-job form input', function(e) {
        if (e.which == 13) e.preventDefault();
    });
    $(document).on('click', '#transfer-job form .btn-primary', POW.jobs.submit_transfer_job);
    $(document).on('change', '#jobs input[type=checkbox]', POW.jobs.toggle_batch_actions);
    $(document).on('change', '#create-job select[name=from_state],#create-job select[name=to_state]', POW.jobs.filter_names);

    POW.jobs.prepend_button();

    update_multiselect('select[multiple]');

    // start -- remove this in the future
    // current requirement: warehouse to kiosk, kiosk to warehouse only
    $(document).on('change', '#create-job select[name=location_from_type]', function() {
        POW.jobs.sync_from_to($(this), '#create-job select[name=location_to_type]')
    })
    $(document).on('change', '#create-job select[name=location_to_type]', function() {
        POW.jobs.sync_from_to($(this), '#create-job select[name=location_from_type]')
    })
    // end --
});

POW.jobs.prepend_button = function()
{
    $('#jobs_table_filter')
        .closest('.row')
        .find('div')
        .first()
        .prepend($('#current-jobs .action-buttons').html());
}

POW.jobs.toggle_batch_actions = function() {
    $('.bulk-actions .btn-primary').prop('disabled', $('#jobs form').serialize() ? false : true);
}

POW.jobs.submit_jobs = function(e)
{
    var el = $(this);
    var data = $('#jobs form').serialize();

    e.preventDefault();

    if (!data) {
        return bootstrap_alert('danger', 'Please select at least one job');
    }
    else {
        if (el.is('[destructive]')) {
            destructive_swal('Are you sure?', "This can't be undone", 'Delete', function () {
                show_backdrop();
                POW.msg.rc('post', el.data('url'), data);
            });
        }
        else {
            show_backdrop();
            POW.msg.rc('post', el.data('url'), data);
        }
    }
}

POW.jobs.sync_from_to = function(target, other)
{
    $(other).val(target.val() == 'inventory_location' ? 'kiosk' : 'inventory_location');

    POW.forms.process_hide_show(null, '#create-job');
}

POW.jobs.open_transfer_job = function() 
{
    var el = $(this);
    var transfer_ids = [];

    $('#jobs input[type=checkbox]:checked').each(function() {
        transfer_ids.push($(this).val());
    });

    if (!transfer_ids.length) {
        return bootstrap_alert('danger', 'No jobs selected');
    }

    POW.modals.show('transfer-job', el.data('url'));
}

POW.jobs.submit_transfer_job = function(e)
{
    var form = $(this).closest('form');
    var data = form.serialize()+'&'+$('#jobs form').serialize();

    POW.forms.submit.apply(this, [e, data]);
}

POW.jobs.filter_names = function(e)
{
    var select = $(this);

    POW.msg.rc('post', select.data('target')+'/'+select.attr('name'), select.closest('form').serialize());
}
