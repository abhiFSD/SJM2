<div class="row">
    <div class="col-md-12">
        <h2>Assign SKUs</h2>
    </div>
</div>

<?php if ($this->session->flashdata('msg') != ""): ?>
    <div class="alert alert-warning"><?php print $this->session->flashdata('msg'); ?></div>
<?php endif; ?>

<div id="msg" class="alert alert-success" style="display: none;"></div>

<div class="row">
    <div class="col-md-12">
        <form class="new top-filter" method="post" action="<?php print site_url('products/assignskunew'); ?>" get-html interactive data-target="#assignsku">
            <input type="hidden" name="_thisisapost" value="thisisapost">
            <?php $options = [
                'product' => 'Product',
                'equipment' => 'Equipment',
                'part' => 'Part',
                'material' => 'Material',
            ]; ?>
            <div class="form-group">
                <?php print form_dropdown('item_category_type', $options, 'product', 'data-placeholder="Item Type" multiple class="filter_crit form-control multiselect"'); ?>
            </div>
        </form>
    </div>
</div>

<div id="assignsku" class="datatable table-responsive" data-sort-column="0">
    <?php $this->load->view('products/sections/table', ['items' => $items, 'warehouses' => $warehouses]); ?>
</div>

<script>
function updateAssigment(el, location) {
    $('#msg').hide();

    $.get("<?php echo site_url('products/saveassignment'); ?>/"+ el.value + "/"+ location + "/"+ el.checked, function (data) {
        $('#msg').html(data);
        $('#msg').show();
    });
}
function toggle(classname, source) {
    $('.'+ classname).each(function (index, el) {
        if(source.checked != el.checked ) {
            el.click();
        }
    });
}
</script>
