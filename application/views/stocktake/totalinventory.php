<div class="row">
    <div class="col-md-12">
        <h2>Total Inventory <span class="pull-right desktop-only"></span></h2>
    </div>
</div>        
<div class="row">
    <div class="col-md-12">
        <form method="post" action="<?php print site_url('stocktakenew/totalinventory'); ?>" class="new top-filter" get-html interactive data-target="#total-inventory">
            <input type="hidden" name="_thisisapost" value="thisisapost">
            <div class="form-group">
                <?php print form_dropdown('location[]', $locations, [], 'multiple data-placeholder="Location"'); ?>
            </div>
            <?php $options = [
                'product' => 'Product',
                'equipment' => 'Equipment',
                'part' => 'Part',
                'material' => 'Material',
            ]; ?>
            <div class="form-group">
                <?php print form_dropdown('item_category_type[]', $options, '', 'data-placeholder="Item Type" multiple class="filter_crit form-control donotsort multiselect"'); ?>
            </div>
            <div class="form-group pull-right">
                <input type="submit" class="btn btn-primary" name="action" value="Download">
            </div>
        </form>
        <hr />
    </div>
</div>

<?php if (isset($message) && $message): ?>
    <div class="alert alert-danger">
        The Stock on Hand has only been updated for some products due to a subsequent overriding Stocktake.  All adjustments have been entered into the Stock Movement Log.
    </div>
<?php endif; ?>
    
<div id="total-inventory" class="datatable table-responsive" data-sort-column="1" data-no-filter-column="2,3,4">
    <?php $this->load->view('stocktake/sections/total_inventory_table', ['items' => $items]); ?>
</div>