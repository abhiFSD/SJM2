<div class="row page-header">
    <div class="col-sm-9">
        <h3 class="margin-0"><?php echo $action; ?> Kiosk</h3>
    </div>
    <div class="col-sm-3 text-right">
        <a href="<?php echo site_url('kiosk/all'); ?>" class="btn btn-primary">Back</a>
    </div>
</div>

<div class="row">
    <div class="col-md-6 no-gutters">
        <form method="post"  id="kioskform">
            <?php print form_hidden('type', 'kiosk'); ?>
            <div class="form-group">
                <label>Kiosk Number</label>
                <?php print role_form_input(2, 'number', $kiosk_number, 'class="form-control" data-url="'.site_url('kiosk/is_unique').'" required'); ?>
                <span id="suggestions"></span>
            </div>
            <div class="form-group">
                <label>Model</label>
                <?php print role_form_dropdown(2, 'kiosk_model_id', $kiosk_model_options, $kiosk->kiosk_model_id, 'class="form-control" required'); ?>
            </div>
            <div class="form-group">
                <label>Kiosk Owner</label>
                <?php print role_form_dropdown(2, 'party_type_allocation_id', $party_type_allocation_options, $kiosk->party_type_allocation_id, 'class="form-control"'); ?>
            </div>
            <div class="form-group">
                <label>Status</label>
                <?php print role_form_dropdown(2, 'status', $status_options, $kiosk->status, 'class="form-control" required'); ?>
            </div>
            <div class="form-group">
                <label>Purchase Date</label>
                <?php if ($this->session->userdata('role_id') <= 2): ?>
                    <input type="datetime-local" class="form-control" name="date_purchased" value="<?php echo $date_purchased; ?>">
                <?php else: ?>
                    <p><?php print $date_purchased; ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>Warranty-Labour</label>
                <?php print role_form_input(2, 'warranty_labour', $kiosk->warranty_labour, 'class="form-control"'); ?>
            </div>
            <div class="form-group">
                <label>Warranty-Parts</label>
                <?php print role_form_input(2, 'warranty_parts', $kiosk->warranty_parts, 'class="form-control"'); ?>
            </div>
            
            <div class="col-md-12">
                <?php if ($this->session->userdata('role_id') <= 2): ?>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary send" value="Submit" name="save" />&nbsp;<a href="<?php echo site_url('kiosk/all'); ?>" class="btn">Cancel</a>
                    </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
