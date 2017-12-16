<div class="row page-header">
    <div class="col-sm-9">
        <h3 class="margin-0"><?php echo $action; ?> Inventory Location</h3>
    </div>
    <div class="col-sm-3 text-right">
        <a href="<?php echo site_url('inventorylocation/all'); ?>" class="btn btn-primary">View All Inventory Locations</a>
    </div>
</div>

<form method="post" id="form">
    <div class="col-md-12">
        <div class="col-md-6">
            <div class="form-group">
                <label>Name</label>
                <?php print role_form_input(2, 'name', $inventory_location->name, 'class="form-control" required'); ?>
            </div>
            <div class="form-group">
                <label>Address</label>
                <?php print role_form_input(2, 'street_address_1', $inventory_location->street_address_1, 'class="form-control" required'); ?>
                <?php print role_form_input(2, 'street_address_2', $inventory_location->street_address_2, 'class="form-control"'); ?>
            </div>
            <div class="form-group">
                <label>Suburb</label>
                <?php print role_form_input(2, 'suburb', $inventory_location->suburb, 'class="form-control" required'); ?>
            </div>
            <div class="form-group">
                <label>State</label>
                <?php print role_form_input(2, 'state', $inventory_location->state, 'class="form-control" required'); ?>
            </div>
            <div class="form-group">
                <label>Postcode</label>
                <?php print role_form_input(2, 'post_code', $inventory_location->post_code, 'class="form-control" required'); ?>
            </div>
            <div class="form-group">
                <label>Status</label>
                <?php print role_form_dropdown(2, 'active', $status_options, $inventory_location->active ? '1' : '0', 'class="form-control"'); ?>
            </div>
            <?php if ($this->session->userdata('role_id') <= 2): ?>
                <div class="form-group">
                    <input type="hidden" name="inventory_location_id" value="<?php echo $inventory_location->id; ?>" />
                    <input type="submit" class="btn btn-primary" value="Submit" name="save" />&nbsp;<a href="<?php echo site_url('inventorylocation/all'); ?>" class="btn ">Cancel</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</form>
