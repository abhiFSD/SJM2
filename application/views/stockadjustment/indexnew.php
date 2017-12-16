<div class="row">
    <div class="col-md-12" id="stock-adjustment-form">
        <form action="" method="post" id="form" data-method-prefix="indexNew" novalidate enctype="multipart/form-data">
            <h2>Stock Adjustment Form</h2>

            <div class="alert-custom">
                <div class="col-md-6">

                    <div class="form-group">
                        <label>Location: *</label>
                        <select id="location" name="location" onchange="indexNewChangeLocation();" class="form-control">
                            <option value="">Select Location</option>
                            <?php foreach ($locations as $location): ?>
                                <option value="<?php echo $location->id ?>"><?php echo $location->name; ?></option>
                            <?php endforeach; ?>
                        </select>

                    </div>
                    <div class="form-group">
                        <label>Adjustment Type: *</label>
                        <select id="adjustment" name="adjustment"
                                onchange="indexNewChangeLocation();indexNewchangeAdjustment();" class="form-control">
                            <option value="">Select Adjustment</option>
                            <option value="1">Stock Order</option>
                            <option value="2">Over Pick</option>
                            <option value="4">Pick List Total</option>
                            <option value="6">Pick Adjustment</option>
                            <!--  <option value="3">Damaged Stock Removed From Kiosk</option> -->
                            <option value="8">Swapped Out From Kiosk</option>
                            <option value="7">Other - Add to Stock On Hand</option>
                            <option value="9">Other - Subtract from Stock On Hand</option>
                            <option value="10">Interstate Transfer - Sent</option>
                            <option value="11">Interstate Transfer - Received</option>
                            <option value="12">Online Orders</option>
                            <option value="13">Replacement</option>
                            <option value="14">Sample / Gift</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group adjustment_fields field_1">
                        <label>Order Number: *</label>
                        <input type="text" name="ordernumber" id="field_1" value="" class="form-control">
                    </div>
                    <div class="form-group adjustment_fields field_2">
                        <p>
                            <label>Kiosk Number:</label>
                            <select class="form-control" name="stock_machine" value="" id="field_2">
                                <option value="">Select Kiosk</option>
                                <?php foreach ($deployments as $deployment): ?>
                                    <option
                                        value="<?php echo $deployment->number . '-' . $deployment->name; ?>"><?php echo $deployment->number . '-' . $deployment->name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </p>
                    </div>
                    <div class="form-group adjustment_fields field_3">
                        <p>
                            <label>Kiosk Number:</label>
                            <select class="form-control" name="damaged_stock_machine" value="" id="field_3">
                                <option value="">Select Kiosk</option>
                                <?php foreach ($deployments as $deployment): ?>
                                    <option
                                        value="<?php echo $deployment->number . '-' . $deployment->name; ?>"><?php echo $deployment->number . '-' . $deployment->name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </p>
                    </div>
                    <div class="form-group adjustment_fields field_6">
                        <p>
                            <label>Kiosk Number: *</label>
                            <select class="form-control" name="pick_adjustment" value="" id="field_6">
                                <option value="">Select Kiosk</option>
                                <?php foreach ($deployments as $deployment): ?>
                                    <option
                                        value="<?php echo $deployment->number . '-' . $deployment->name; ?>"><?php echo $deployment->number . '-' . $deployment->name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </p>
                    </div>
                    <div class="form-group adjustment_fields field_7">
                        <label>Reason: *</label>
                        <input type="text" name="other7" value="" id="field_7" class="form-control">
                    </div>
                    <div class="form-group adjustment_fields field_8">
                        <p>
                            <label>Kiosk Number: *</label>
                            <select class="form-control" name="swapped_machine" value="" id="field_8">
                                <option value="">Select Kiosk</option>
                                <?php foreach ($deployments as $deployment)
                                {
                                    ?>
                                    <option
                                        value="<?php echo $deployment->number . '-' . $deployment->name; ?>"><?php echo $deployment->number . '-' . $deployment->name; ?></option>
                                <?php } ?>
                            </select>
                        </p>
                    </div>
                    <div class="form-group adjustment_fields field_9">
                        <label>Reason: *</label>
                        <input type="text" name="other9" value="" id="field_9" class="form-control">
                    </div>
                    <div class="form-group adjustment_fields field_14">
                        <label>Reason: *</label>
                        <input type="text" name="other14" value="" id="field_14" class="form-control">
                    </div>
                    <div id="date-block">
                        <div class="form-group">
                            <label><span id="date-label">Adjustment Date & Time</span>: *</label>
                            <input type="datetime-local" name="date" id="date" class="form-control"
                                   value="<?php echo $date . "T" . $time; ?>" step="1"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Upload File: *</label>
                        <input type="file" name="upload_file" id="file" class="form-control"/>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-12">
                    <div id="typefilter" style="display: none">
                        <label>Filter Items </label>
                        <select name="type[]" id="indexNewpdttype" onchange="indexNewChangeLocation()" multiple>
                            <option value="Product">Product</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Part">Part</option>
                            <option value="Material">Material</option>
                        </select>
                        <span class="pull-right">
                            <input type="submit" class="btn btn-primary" value="Submit" name="save"/>&nbsp;
                            <a href="<?php echo site_url('stocktakenew/listall'); ?>" class="btn ">Cancel</a>
                        </span>
                    </div>
                    <div id="productresults">
                    </div>
                </div>
            </div>
        </form>
        <input type="hidden" name="chkDate" id="indexNewIsFutureDate" value="<?php echo date('Y-m-d H:i:s'); ?>">
    </div>
</div>