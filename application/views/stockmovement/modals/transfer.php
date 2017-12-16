<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Transfer</h4>
        </div>
        <form class="form-horizontal" ajax method="post" action="<?php print site_url('stockmovement/transfer'); ?>">
            <div class="modal-body">
                <input type="hidden" name="modal-id" value="transfer-job">
                <div class="form-group row">
                    <label class="control-label col-xs-12 col-sm-3">Now With Type</label>
                    <div class="col-xs-12 col-sm-9">
                        <select name="location_type" class="form-control">
                            <option value="Freight Provider" data-show="#transfer-job .freight_providers,#transfer-job .tracking-link" data-hide="#transfer-job .staff" selected>Freight Provider</option>
                            <option value="Staff" data-show="#transfer-job .staff" data-hide="#transfer-job .freight_providers,#transfer-job .tracking-link">Staff</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row freight_providers">
                    <label class="control-label col-xs-12 col-sm-3">Now With Name</label>
                    <div class="col-xs-12 col-sm-9">
                        <?php print form_dropdown('freight_provider', $freight_providers, null, 'class="form-control" required'); ?>
                    </div>
                </div>
                <div class="form-group row staff" style="display: none;">
                    <label class="control-label col-xs-12 col-sm-3">Now With Name</label>
                    <div class="col-xs-12 col-sm-9">
                        <?php print form_dropdown('staff', $staff, null, 'class="form-control" required'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-xs-12 col-sm-3">Handover Time</label>
                    <div class="col-sx-12 col-sm-5">
                        <input type="date" name="handover_date" class="form-control" value="<?php print date('Y-m-d'); ?>">
                    </div>
                    <div class="col-sx-12 col-sm-4">
                        <input type="time" name="handover_time" class="form-control" value="<?php print date('H:i'); ?>">
                    </div>
                </div>
                <div class="form-group row tracking-link">
                    <label class="control-label col-xs-12 col-sm-3 not-bold">Tracking link</label>
                    <div class="col-sx-12 col-sm-9">
                        <input type="text" class="form-control" name="tracking_link">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-xs-12 col-sm-3 not-bold"></label>
                    <div class="col-xs-12 col-sm-9">
                        <textarea placeholder="Note" name="note" class="form-control"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary">Submit</button>
                <button class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>
