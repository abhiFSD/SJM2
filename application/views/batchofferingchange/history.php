<form method="post" action="<?php echo site_url('batchofferingchange/filter_history'); ?>">
    <div class="row1">
        <h3 class="page-header">
            Planagram History
            <span class="pull-right">
                <a href="<?php echo site_url('batchofferingchange/all/'); ?>"  class="btn btn-primary">Planagrams</a>
            </span>
        </h3>
        <!-- filters start here -->
            <div class="row">
                <div class="col-md-3 col-sm-6 form-group">
                    <select name="kiosk_name" id="kiosk_name_filter" class="form-control">
                        <option value="">Select Kiosk</option>
                        <?php foreach($kiosks as $kiosk_name) { ?>
                        <option value= "<?php echo $kiosk_name->id;?>"><?php echo $kiosk_name->number ." - ". $kiosk_name->location_name; ?></option>
                        <?php  } ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <input type="text" value="" name="min_price" id="min_price_filter" class="form-control" placeholder="Min Price">
                        </div>
                        <div class="col-sm-6">
                            <input type="text" value="" name="max_price" id="max_price_filter" class="form-control" placeholder="Max Price">
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 form-group">
                    <select name="kiosk_model" id="kiosk_model_filter" class="form-control">
                        <option value="">Select Kiosk Model</option>
                        <?php foreach($kiosk_models as $kiosk_model) { ?>
                        <option value= "<?php echo $kiosk_model->id;?>"><?php echo $kiosk_model->make.' / '.$kiosk_model->name;?></option>
                        <?php  } ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 form-group">
                    <select name="capacity" id="capacity_filter" class="form-control">
                        <option value="">Select Capacity</option>
                        <?php sort($capacities); foreach($capacities as $capacity) { ?>
                        <option value= "<?php echo $capacity;?>"><?php echo $capacity;?></option>
                        <?php  } ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 form-group">
                    <select name="state" id="state_filter" class="form-control">
                        <option value="">Select State</option>
                        <?php foreach($states as $state) { ?>
                        <option value= "<?php echo $state;?>"><?php echo $state;?></option>
                        <?php  } ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 form-group">
                    <select name="position" id="position_filter" class="form-control">
                        <option value="">Select Position</option>
                        <?php sort($positions); foreach($positions as $position) { ?>
                        <option value= "<?php echo $position;?>"><?php echo $position;?></option>
                        <?php  } ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 form-group">
                    <select name="par" id="par_filter" class="form-control">
                        <option value="">Select Par</option>
                        <?php sort($pars); foreach($pars as $par) { ?>
                        <option value= "<?php echo $par;?>"><?php echo $par;?></option>
                        <?php  } ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 form-group">
                    <select name="site_category" id="site_category_filter" class="form-control">
                        <option value="">Select Site Category</option>
                        <?php foreach($site_categories as $site_category) { ?>
                        <option value= "<?php echo $site_category;?>"><?php echo $site_category;?></option>
                        <?php  } ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 form-group">
                    <select name="sku_id" id="product_filter" class="form-control">
                        <option value="">Select SKU</option>
                        <?php foreach(POW\Sku::get_all() as $sku) { ?>
                        <option value= "<?php echo $sku->id; ?>"><?php echo $sku->name;?></option>
                        <?php  } ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 form-group">
                    <select name="status" id="status_filter" class="form-control">
                        <option value="">Select Status</option>
                        <option value="current">Current</option>
                        <option value="queued">Queued</option>
                    </select>
                </div>
                <div class="clearfix"></div>
                <div id="filter_button_box">
                    &nbsp;&nbsp; <button id="apply_filters_button" type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        <!-- filters end here -->
    </div>
</form>
<div class="row" id="result">
</div>
