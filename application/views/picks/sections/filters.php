<ul class="<?php print $ul_class; ?>" style="margin-left:0;">
    <li class="sidebar-brand" style="">
        <?php if (empty($is_mobile)): ?>
            <a href="#menu-toggle"  id="menu-toggle" style="float:right;" >
                <i class="fa fa-filter" style="font-size:20px !Important;" aria-hidden="true" aria-hidden="true"></i>
            </a>
        <?php endif; ?>
        <h4>Filters</h4>
    </li>
    <li>
        <div class="form-group">
            <label class="<?php print $label_class; ?>">Select Status</label>
            <select data-placeholder="Select Status" name="status" multiple class="filter_crit form-control multiselect">
                <option value= "-1" selected>No Outstanding Picks</option>
                <option value= "Pick generated" selected>Pick Generated</option>
                <option value= "Partially picked" selected>Partially Picked</option>
                <option value= "Fully picked" selected>Fully Picked</option>
                <option value= "Warehouse Returns" selected>Warehouse Returns</option>
            </select>
        </div>
    </li>
    <li>
        <div class="form-group">
            <label class="<?php print $label_class; ?>">Select State</label>
            <select data-placeholder="Select State" name="state" multiple class="filter_crit form-control multiselect">
                <?php foreach($states as $state) { ?>
                    <option value= "<?php echo $state;?>" selected><?php echo $state;?></option>
                <?php  } ?>
            </select>
        </div>
    </li>
    <li>
        <div class="form-group">
            <label class="<?php print $label_class; ?>">Select Site &nbsp; </label>
            <select data-placeholder="Select Site" name="site_filter" multiple class="filter_crit form-control multiselect">
                <?php  foreach($sites as $site) { ?>
                    <option value= "<?php echo $site->id;?>" selected><?php echo $site->name;?></option>
                <?php  } ?>
            </select>
        </div>
    </li>
    <li>
        <div class="form-group kiosk_name_filter">
            <label class="<?php print $label_class; ?>" >Select Kiosk</label>
            <select data-placeholder="Select Kiosk" name="kiosk_name" class="filter_crit form-control multiselect" multiple >
                <?php foreach($kiosks as $kiosk_name) { ?>
                    <option value= "<?php echo $kiosk_name->number;?>" selected><?php echo $kiosk_name->number ." - ". $kiosk_name->location_name; ?></option>
                <?php  } ?>
            </select>
        </div>
    </li>
    <li>
        <div>
            <button type="submit" class="btn btn-primary apply_filters_button <?php print $label_class; ?>">
                <i class="fa fa-filter" aria-hidden="true"></i> Filter
            </button>
        </div>
    </li>
    <?php if (empty($is_mobile)): ?>
        <li style="height: 250px"></li>
    <?php endif; ?>
</ul>