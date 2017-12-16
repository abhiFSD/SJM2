<div class="row page-header">
    <div class="col-sm-9">
        <h3 class="margin-0">Stock Movement Log</h3>
    </div>
    <div class="col-sm-3 desktop-only text-right">
            <a class="btn btn-primary download-btn" data-post-download data-target="#frmfilter" data-url="<?php print site_url('stockmovement/downloadstock'); ?>"><i class="fa fa-download"></i>&nbsp;Download&nbsp;</a>
    </div>
</div>

<div class="row">
    <div class="col-md-12 alert-custom alert-success1">
        <div id="filter_section" class="row filter_section" style="display: block;">
            <form method="get" name="frmfilter" id="frmfilter">
                        <div class="col-md-4 form-group">
                            <label>Warehouse: </label>
                            <?php
                                $options = ['0' => 'Select Warehouse'] + $locations;
                                print form_dropdown('location', $options, $locationid, 'id="location" class="form-control"');
                            ?>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Kiosk: </label>
                            <?php
                                $options = array('' => 'Select Kiosk');
                                foreach ($kiosks as $kiosk_name) {
                                    $options[$kiosk_name->id] = $kiosk_name->number;
                                }
                                print form_dropdown('kiosks', $options, $kiosk, 'id="kiosks" class="form-control filter_crit"');
                            ?>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Location Type: </label>
                            <?php 
                                $options = array(
                                    '' => 'Select Location Type',
                                    'inventory_location' => 'Warehouse',
                                    'kiosk' => 'Kiosk',
                                );
                                print form_dropdown('location_type', $options, $location_type, 'class="form-control"');
                            ?>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Description: </label>
                            <input type="text" name="description" placeholder="Description" id="description" class="form-control" value="<?php if (isset($description)) print $description; ?>">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>SKU: </label>
                            <?php $options = ['' => 'Select SKU'] + $products; ?>
                            <?php print form_dropdown('product', $options, $productname, 'id="product" class="form-control"'); ?>
                        </div>
                        <?php $options = [
                            '' => 'Select Item Type',
                            'product' => 'Product',
                            'equipment' => 'Equipment',
                            'part' => 'Part',
                            'material' => 'Material',
                        ]; ?>
                        <div class="col-md-4 form-group">
                            <label>Item Type: </label>
                            <?php print form_dropdown('item_category_type', $options, $item_category_type, 'data-placeholder="Item Type" class="form-control donotsort"'); ?>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Movement Type: </label>
                            <?php 
                                $options = array(
                                    '' => 'Select Movement Type',
                                    'Sale' => 'Sale',
                                    'Pick' => 'Pick',
                                    'Replenishment' => 'Replenishment',
                                    'Stocktake' => 'Stocktake',
                                    'Stock Order' => 'Stock Order',
                                    'Over Pick' => 'Over Pick',
                                    'Pick List Total' => 'Pick List Total',
                                    'Pick Adjustment' => 'Pick Adjustment',
                                    'Swapped Out From Kiosk' => 'Swapped Out From Kiosk',
                                    'Other - Add to Stock On Hand' => 'Other - Add to Stock On Hand',
                                    'Other - Subtract from Stock On Hand' => 'Other - Subtract from Stock On Hand',
                                    'Interstate Transfer - Sent' => 'Interstate Transfer - Sent',
                                    'Interstate Transfer - Received' => 'Interstate Transfer - Received',
                                    'Online Orders' => 'Online Orders',
                                    'Replacement' => 'Replacement',
                                    'Sample / Gift' => 'Sample / Gift',
                                );
                                print form_dropdown('adjustment', $options, $adjustment, 'id="adjustment" class="form-control"');
                            ?>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Movement Start Date:</label>
                            <input type="date" id="mindate" name="mindate" class="form-control"
                            value="<?php echo(isset($mindate) ? $mindate : ''); ?>"/>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Movement End Date:</label>
                            <input type="date" name="maxdate" id="maxdate" class="form-control"
                            value="<?php echo(isset($maxdate) ? $maxdate : ''); ?>"/>
                        </div>
                        <?php if (!empty($user_id_filter)): ?>
                            <div class="col-md-4 form-group">
                                <label>User</label>
                                <select name="user_id" class="form-control">
                                    <option></option>
                                    <option value="<?php print $user_id_filter; ?>" selected><?php print POW\User::with_id($user_id_filter)->display_name; ?></option>
                                </select>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($datetime_3m)): ?>
                            <div class="col-md-4 form-group">
                                <label>DateTime</label>
                                <select name="datetime_3m" class="form-control">
                                    <option></option>
                                    <option value="<?php print $datetime_3m; ?>" selected><?php print $datetime_3m; ?></option>
                                </select>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-4 form-group pull-right submit-btn-mvlog text-right">
                            <input type="submit" name="action" value="Filter" class="btn btn-primary"/>
                        </div>
            </form>
        </div>
    </div>
</div>

<div id="warehouse-inventory" class="datatable-json" data-sort-column="0" data-sort-direction="desc">  
    <?php if (isset($skipped)): ?>
        <div class="alert alert-warning">
            <strong>Following entries are skipped:<br/></strong>
            <ul>
                <?php foreach ($skipped as $issue) { ?>
                    <li><?php echo $issue['value'] ?> => <?php echo $issue['reason'] ?></li>
                <?php } ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php $this->load->view('stockmovement/sections/table', ['items' => $items]); ?>
</div>

<?php if ($cleandata): ?>
    <script>
        localStorage.formValues = "";
    </script>
<?php endif; ?>

