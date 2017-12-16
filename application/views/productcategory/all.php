




<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
  $('#items').dataTable({ "lengthMenu": [ 25, 50, 100, 200 ], "pageLength": 200, fixedHeader: true});
} );
var filter = function() {
  var url = "<?php echo site_url('product/all'); ?>/" + document.getElementById('status').value;
  document.location.href= url;
}
</script>

<div class="row">
  <h3 class="page-header">Item Categories <span class="pull-right"><a href="<?php echo site_url('productcategory/manage'); ?>" class="btn btn-primary">Add New Category</a>&nbsp;</span></h3>
  <?php if(count($categories) > 0 ): ?>
    <?php if ($this->session->flashdata('msg') != ""): ?>
      <div class="alert alert-warning"><?php echo $this->session->flashdata('msg'); ?></div>
    <?php endif; ?>
    <table class="table table-striped" id="items">
      <thead>
        <tr>
          <th>Name</th>
          <th>Default attributes</th>
          <th>Parent</th>
          <th>Type</th>
          <th>Edit</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $c = array();
          foreach($categories as $k => $category) {
          $c[$category->product_category_id]['name']= $category->name;
          }
        ?>
        <?php foreach($categories as $category): ?>
          <?php $cat = (array)$category; ?>
          <tr>
            <td><?php echo $category->name; ?></td>
            <td><?php echo $category->attributes; ?></td>
            <td>
              <?php if($cat['parent_product_category_id']!=0) echo  $c[$cat['parent_product_category_id']]['name']; ?>
            </td>
            <td><?php echo ucwords($category->item_category_type); ?></td>
            <td><a href="<?php echo site_url('productcategory/manage/'. $cat['product_category_id']); ?>" title="Edit Category"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;</td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="alert alert-danger">No categories found currently</div>
  <?php endif; ?>
</div>