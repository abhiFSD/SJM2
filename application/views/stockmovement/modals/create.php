<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Create Stock Movement</h4>
        </div>
        <form class="" ajax method="post" action="<?php print site_url('stockmovement/create'); ?>">
            <div class="modal-body">
                <input type="hidden" name="modal-id" value="create-job">
                <input type="hidden" name="location_from_type" value="inventory_location">
                <input type="hidden" name="location_to_type" value="kiosk">

                <div class="row">

                    <!-- From -->
                    <div class="col-md-5 col-sm-12">
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label class="control-label">FROM</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <input type="radio" disabled> Kiosk
                                <input type="radio" checked> Warehouse
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <?php print form_dropdown('from_state', array_merge(['' => 'Select State'], $states), null, 'class="form-control" data-target="'.site_url('stockmovement/create_job_names').'"'); ?>
                            </div>
                        </div>
                        <div class="form-group row from-inventory_location">
                            <div class="col-sm-12">
                                <?php print form_dropdown('from_inventory_location', $inventory_locations, null, 'class="form-control" required'); ?>
                            </div>
                        </div>
                        <div class="form-group row from-kiosk" style="display: none;">
                            <div class="col-sm-12">
                                <div class="multi-select">
                                    <?php foreach ($kiosks as $kiosk): ?>
                                        <div>
                                            <label class="control-label not-bold">
                                                <input type="checkbox" name="from_kiosk_id[]" required value="<?php print $kiosk->id; ?>">
                                                <?php print $kiosk->number.' - '.$kiosk->location_name; ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- To -->
                    <div class="col-md-7 col-sm-12">
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label class="control-label">TO</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <input type="radio" checked> Kiosk
                                <input type="radio" disabled> Warehouse
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <?php print form_dropdown('to_state', array_merge(['' => 'Select State'], $states), null, 'class="form-control" data-target="'.site_url('stockmovement/create_job_names').'"'); ?>
                            </div>
                        </div>
                        <div class="form-group row to-inventory_location" style="display: none;">
                            <div class="col-sm-12">
                                <?php print form_dropdown('to_inventory_location', $inventory_locations, null, 'class="form-control" required'); ?>
                            </div>
                        </div>
                        <div class="form-group row to-kiosk">
                            <div class="col-sm-12">
                                <div class="multi-select">
                                    <div class="wrapper">
                                        <?php foreach ($kiosks as $kiosk): ?>
                                            <div>
                                                <label class="control-label not-bold">
                                                    <input type="checkbox" name="to_kiosk_id[]" required value="<?php print $kiosk->id; ?>">
                                                    <?php print $kiosk->number.' - '.$kiosk->location_name; ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="form-group row">
                    <label class="control-label col-xs-12 col-sm-2 not-bold"></label>
                    <div class="col-sm-12">
                        <textarea placeholder="Note" name="notes" class="form-control"></textarea>
                    </div>
                </div>
            </div>
           <!--Modification: Cancel Button removed -->
            <div class="modal-footer">
                <button class="btn btn-primary" data-submit>Submit</button>

            </div>
        </form>
    </div>
</div>
