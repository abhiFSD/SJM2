<div class="modal fade" id="AddPositionModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Position</h4>
            </div>
            <form name="add-position-form" id="add-position-form">
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <input type="text" required=""  placeholder="Position Number" class="form-control" name="position-number" id="position-number"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <label for="message-text" class="control-label">Select kiosk(s) to add position to:</label>
                            <div id="kiosk-select-p" class="multi-select">
                                <?php foreach($kiosks as $kiosk_name) { ?>
                                <div id="block<?php echo $kiosk_name->id;?>" class="blocks">
                                    <input type="checkbox" name="kiosk-numbers[]" data-id='<?php echo $kiosk_name->id;?>' value="<?php echo $kiosk_name->id;?>" class="kiosk-numbers"/>
                                    <span class="checkbox-label"><?php echo $kiosk_name->number ." - ". $kiosk_name->location_name; ?></span>
                                </div>
                                <?php  } ?>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="submit" id="add-position-form-btn" class="btn btn-primary">Add</button>
                    <span class="btn btn-default" data-dismiss="modal">Cancel</span>
                </div>
            </form>
        </div>
    </div>
</div>