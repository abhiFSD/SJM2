<div class="row">
    <div class="col-md-12">
        <h2>Inventory By Warehouse</h2>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <form class="new top-filter" method="post" action="<?php print site_url('stocktakenew/listall'); ?>" get-html interactive data-target="#warehouse-inventory">
            <input type="hidden" name="_thisisapost" value="thisisapost">
            <div class="form-group">
                <?php print form_dropdown('warehouse_ids[]', $locations, $warehouse_ids, 'data-placeholder="Location" multiple class="form-control multiselect"'); ?>
            </div>
            <div class="form-group">
                <?php print form_dropdown('item_category_type[]', $options, 'product', 'data-placeholder="Item Type" multiple class="filter_crit form-control donotsort multiselect"'); ?>
            </div>
            <div class="form-group pull-right">
                <button class="btn btn-primary" name="action" value="Download"><i class="fa fa-file-excel-o"></i>&nbsp;Download</button>
            </div>
        </form>
        <hr>
    </div>
</div>
<?php
    if (isset($message) && $message)
    {
        echo '<div class="alert alert-danger">The Stock on Hand has only been updated for some products due to a subsequent overriding Stocktake.  All adjustments have been entered into the Stock Movement Log.</div>';
    }
?>
<div id="warehouse-inventory" class="datatable table-responsive" data-sort-column="1" data-no-filter-column="1,4,5,6">
    <?php if (!empty($skipped)): ?>
            <div class="alert alert-warning">
                <strong>Following entries are skipped:<br /></strong>
                <ul>
                    <?php foreach($skipped as $issue): ?>
                            <li><?php echo $issue['value']?> => <?php echo $issue['reason']?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
    <?php endif; ?>
    <?php $this->load->view('stocktake/sections/warehouse_inventory', ['items' => $items]); ?>
</div>