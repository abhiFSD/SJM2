<div id="dialog-form2" title="Add License Agreement">
    <form method="post" id="agreement_form" class="form">
        <div class="col-md-12">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Licensor:</label>
                    <input name="la_licensor_name" id="la_licensor_name" class="form-control"  data-id="<?php echo (isset($deployment)?$deployment->licensor_id:'');?>" value="<?php echo (isset($deployment)?$deployment->licensor_name:'');?>" readonly/>
                </div>
                <div class="form-group">
                    <label>Agreement Name:</label>
                    <input type="text" name="la_name" class="form-control" required/>
                </div>
                <div class="form-group">
                    <label>Fixed Component:</label>
                    <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <input type="text" name="la_fixed" class="form-control" id="la_fixed"  haveFixedOrCommission="true"/>
                        <span class="input-group-addon">per month</span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Commission 1 Rate:</label>
                    <div class="input-group">
                        <input type="text" name="la_commission1" id="la_commission1"  class="form-control"  />
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Commission 1 Threshold:</label>
                    <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <input type="text" name="la_commission1_threshold" id="la_commission1_threshold" class="form-control"  />
                        <span class="input-group-addon">per month</span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Commission 2 Rate:</label>
                    <div class="input-group">
                        <input type="text" name="la_commission2" class="form-control" id="la_commission2"  />
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Commission 2 Threshold:</label>
                    <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <input type="text" name="la_commission2_threshold" class="form-control" id="la_commission2_threshold"  />
                        <span class="input-group-addon">per month</span>
                    </div>
                </div>
                <?php $svalue = $evalue = ""; ?>
                <div class="form-group">
                    <label>Start Date:</label>
                    <input type="date" name="la_start_date" class="form-control"  required/>
                </div>
                <div class="form-group">
                    <label>End Date:</label>
                    <input type="date" name="la_end_date" class="form-control"  required/>
                </div>
                <input type="hidden" name="la_status" value ="Active" />
            </div>
            <div class="col-md-6">
            </div>
            <!-- <a href="<?php echo site_url(); ?>" class="btn  btn-primary no-color right-aligned">Home</a> -->
        </div>
    </form>
</div>
