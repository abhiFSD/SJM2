<li>
    <div class="form-group kiosk_name_filter">
        <select placeholder="Kiosk" data-placeholder="Select Kiosk" name="kiosk_name" class="kiosk_name_filter filter_crit form-control multiselect" multiple >
            <?php foreach($kiosks as $kiosk_name): ?>
                    <option value= "<?php echo $kiosk_name->id;?>"
                        <?php if (!empty($planagram_filters['kiosk_name']) && in_array($kiosk_name->id, $planagram_filters['kiosk_name'])) print 'selected'; ?>>
                        <?php echo $kiosk_name->number ." - ". $kiosk_name->location_name; ?>
                    </option>
            <?php  endforeach; ?>
        </select>
    </div>
</li>
<li>
    <div class="form-group kiosk_name_filter">
        <select placeholder="Price Issues" data-placeholder="Select Price Issues" name="price_issue" class="price_issue_filter filter_crit form-control multiselect" multiple >
            <option value="aroma-gt-kiosk"
                <?php if (!empty($planagram_filters['price_issue']) && in_array('aroma-gt-kiosk', $planagram_filters['price_issue'])) print 'selected'; ?>>
                Aroma higher than Kiosk
            </option>
            <option value="kiosk-gt-aroma"
                <?php if (!empty($planagram_filters['price_issue']) && in_array('kiosk-gt-aroma', $planagram_filters['price_issue'])) print 'selected'; ?>>
                Kiosk higher than Aroma
            </option>
        </select>
    </div>
</li>
<li>
    <div class="form-group small-input">
        <input placeholder="Min Price" type="text" value="<?php if (!empty($planagram_filters['min_price'])) print $planagram_filters['min_price']; ?>" name="min_price" class="min_price_filter filter_crit form-control" />
        <input placeholder="Max Price:" type="text" value="<?php if (!empty($planagram_filters['max_price'])) print $planagram_filters['max_price']; ?>" name="max_price" class="max_price_filter filter_crit form-control" />
    </div>
</li>
<li>
    <div class="form-group">
        <select data-placeholder="Kiosk Model" name="kiosk_model" multiple class="kiosk_model_filter filter_crit form-control multiselect">
            <?php foreach($kiosk_models as $kiosk_model): ?>
                    <option value= "<?php echo $kiosk_model->id;?>"
                        <?php if (!empty($planagram_filters['kiosk_model']) && in_array($kiosk_model->id, $planagram_filters['kiosk_model'])) print 'selected'; ?>>
                        <?php echo $kiosk_model->make.' / '.$kiosk_model->name;?>
                    </option>
            <?php  endforeach; ?>
        </select>
    </div>
</li>
<li>
    <div class="form-group">
        <select data-placeholder="Capacity" name="capacity" multiple class="capacity_filter filter_crit donotsort form-control multiselect">
            <!--To be removed by Prashanth -->
            <?php foreach($capacities as $capacity): ?>
                    <option value= "<?php echo $capacity;?>"
                        <?php if (!empty($planagram_filters['capacity']) && in_array($capacity, $planagram_filters['capacity'])) print 'selected'; ?>>
                        <?php echo $capacity;?>
                    </option>
            <?php  endforeach; ?>
        </select>
    </div>
</li>
<li>
    <div class="form-group">
        <select data-placeholder="State" name="state" multiple class="state_filter filter_crit form-control multiselect">
            <?php foreach($states as $state): ?>
                    <option value="<?php echo $state; ?>"
                        <?php if (!empty($planagram_filters['state']) && in_array($state, $planagram_filters['state'])) print 'selected'; ?>>
                        <?php echo $state; ?>
                    </option>
            <?php  endforeach; ?>
        </select>
    </div>
</li>
<li>
    <div class="form-group">
        <select data-placeholder="Position" name="position" multiple class="position_filter filter_crit donotsort form-control multiselect">
            <?php sort($positions);
            foreach($positions as $position): ?>
                <option value= "<?php echo $position;?>"
                    <?php if (!empty($planagram_filters['position']) && in_array($position, $planagram_filters['position'])) print 'selected'; ?>>
                    <?php echo $position;?>
                </option>
            <?php  endforeach; ?>
        </select>
    </div>
</li>
<li>
    <div class="form-group">
        <select data-placeholder="Par" name="par" multiple class="par_filter filter_crit donotsort form-control multiselect">
            <?php asort($pars); foreach($pars as $par): ?>
                    <option value="<?php echo $par; ?>"
                        <?php if (!empty($planagram_filters['par']) && in_array($par, $planagram_filters['par'])) print 'selected'; ?>>
                        <?php echo $par; ?>
                    </option>
            <?php  endforeach; ?>
        </select>
    </div>
</li>
<li>
    <div class="form-group">
        <select data-placeholder="Site Category" name="site_category" multiple class="site_category_filter filter_crit form-control multiselect">
            <?php foreach($site_categories as $site_category): ?>
                    <option value="<?php echo $site_category; ?>"
                        <?php if (!empty($planagram_filters['site_category']) && in_array($site_category, $planagram_filters['site_category'])) print 'selected'; ?>>
                        <?php echo $site_category; ?>
                    </option>
            <?php  endforeach; ?>
        </select>
    </div>
</li>
<li>
    <div class="form-group">
        <select data-placeholder="Item Category" name="item_category" multiple class="product_filter_2 filter_crit form-control multiselect">
            <?php foreach ($product_categories as $product_category): ?>
                    <option value="<?php echo $product_category['product_category_id']; ?>"
                        <?php if ( (isset($product->product_category_id) && $product->product_category_id == $product_category['product_category_id']) || (!empty($planagram_filters['item_category']) && in_array($product_category['product_category_id'], $planagram_filters['item_category'])) ) print 'selected'; ?>>
                        <?php echo $product_category['name']; ?>
                    </option>
            <?php  endforeach; ?>
        </select>
    </div>
</li>
<li>
    <div class="form-group">
        <select data-placeholder="Item" name="product" multiple class="product_filter filter_crit form-control multiselect enable-filtering">
            <?php foreach($products as $product) { ?>
                <option value="<?php echo $product['sku_id']; ?>"
                    <?php if (!empty($planagram_filters['product']) && in_array($product['sku_id'], $planagram_filters['product'])) print 'selected'; ?>>
                    <?php echo (!empty($product['sku_value']) ? $product['sku_value'].' - ' : '').$product['sku_name']; ?>
                </option>
            <?php  } ?>
        </select>
    </div>
</li>
<li>
    <div class="form-group">
        <select data-placeholder="Status" name="status" multiple class="status_filter filter_crit form-control multiselect">
            <option value="active"
                <?php if (!empty($planagram_filters['status']) && in_array('active', $planagram_filters['status'])) print 'selected'; ?>>
                Active
            </option>
            <option value="queued"
                <?php if (!empty($planagram_filters['status']) && in_array('queued', $planagram_filters['status'])) print 'selected'; ?>>
                Queued
            </option>
        </select>
    </div>
</li>
<li>
    <div class="form-group">
        <?php
        $options = [
            0 => 'Temporary',
            1 => 'Permanent',
        ];
        ?>
        <?php print form_dropdown('commit_type', $options, empty($planagram_filters['commit_type']) ? [] : $planagram_filters['commit_type'], 'data-placeholder="Change Type" multiple class="filter_crit form-control multiselect"'); ?>
    </div>
</li>
<li>
    <button type="submit" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
</li>