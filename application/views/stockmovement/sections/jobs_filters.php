<form id="jobs-filters" class="filter" method="post" action="<?php print site_url('stockmovement/jobs'); ?>" get-html data-target="#jobs">
    <ul class="row">
        <li class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php print form_dropdown('location_from_type', array_merge(['' => 'From Type'], $transfer_types), !empty($selected_filters['location_from_type']) ? $selected_filters['location_from_type'] : null, 
                'data-placeholder="From Type" class="form-control" data-ajax="'.site_url('stockmovement/filter_names/from').'"'); 
            ?>
        </li>
        <li class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <select name="location_from_id[]" data-placeholder="From Name" class="form-control multiple" multiple disabled></select>
        </li>
        <li class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php print form_dropdown('from_state[]', $states, !empty($selected_filters['from_state']) ? $selected_filters['from_state'] : null,
                'data-placeholder="From State" multiple class="form-control"');
            ?>
        </li>
        <li class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php print form_dropdown('status[]', $statuses, !empty($selected_filters['status']) ? $selected_filters['status'] : null,
                'data-placeholder="Current Status" multiple class="form-control"');
            ?>
        </li>
        <li class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php print form_dropdown('location_to_type', array_merge(['' => 'To Type'], $transfer_types), !empty($selected_filters['location_to_type']) ? $selected_filters['location_to_type'] : null, 
                'data-placeholder="To Type" class="form-control" data-ajax="'.site_url('stockmovement/filter_names/to').'"'); 
            ?>
        </li>
        <li class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <select name="location_to_id[]" data-placeholder="To Name" class="form-control multiple" multiple disabled></select>
        </li>
        <li class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php print form_dropdown('to_state[]', $states, !empty($selected_filters['to_state']) ? $selected_filters['to_state'] : null,
                'data-placeholder="To State" multiple class="form-control"');
            ?>
        </li>
        <li class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <?php print form_dropdown('location_type[]', $location_types, !empty($selected_filters['location_type']) ? $selected_filters['location_type'] : null,
                'data-placeholder="Current Location Type" multiple class="form-control"');
            ?>
        </li>
        <li class="col-xs-12 text-right">
            <button type="submit" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
        </li>
    </ul>
</form>
