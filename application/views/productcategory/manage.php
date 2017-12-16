<?php 

if(isset($category)) {
	if(isset($category)){
		$cat = (array)$category;
	}
	$action = "Edit";
} 
else {
	$action = "Add";
}

?>

<div class="row">
	<h3 class="page-header"><?php echo $action; ?> Item Category</h3>
	<?php if (isset($msg) && $msg != ""): ?>
		<div class="alert alert-warning"><?php echo $msg; ?></div>
	<?php endif; ?>
	<form method="post" id="form">
		<div class="col-md-12">
			<div class="col-md-6">
				<div class="form-group">
					<input placeholder="Name" type="text" name="name" class="form-control" value="<?php echo (isset($category->name)?$category->name:'')?>" required>
				</div>
				<div class="form-group">
					<select name="parent_id" class="form-control">
						<option value="0">Select parent category</option>
						<?php foreach ($categories as $data): ?>
							<?php $cs = (array)$data; ?>
							<option value="<?php echo $cs['product_category_id'] ?>"
								<?php echo( isset($category->product_category_id) && $category->parent_product_category_id == $cs['product_category_id'])?'selected':''?>>
								<?php echo $data->name ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group">
					<select name="item_category_type" class="form-control" id="type_select" required="">
						<option value="" selected>Select Item Type</option>
						<option value="product"  <?php echo( isset($category) && $category->item_category_type == 'product') ?'selected':''?>>Product</option>
						<option value="equipment"  <?php echo( isset($category) && $category->item_category_type == 'equipment')?'selected':''?>>Equipment</option>
						<option value="part"  <?php echo( isset($category) && $category->item_category_type == 'part')?'selected':''?>>Part</option>
						<option value="material"  <?php echo( isset($category) && $category->item_category_type == 'material')?'selected':''?>>Material</option>
					</select>
				</div>
				<div class="form-group">
					<select name="attribute_ids[]" class="form-control" multiple="">
						<option value="0">Select default attributes for category</option>
						<?php foreach ($attributes as $data): ?>
							<option value="<?php echo  $data['id'] ?>"  <?php echo ( isset($attrs) &&array_key_exists($data['id'],$attrs))?'selected':''?>><?php echo $data['name'] ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group">
					<input type="submit" class="btn btn-primary" value="Submit" name="save">&nbsp;<a href="<?php echo site_url('productcategory/all'); ?>" class="btn ">Cancel</a>
				</div>
			</div>
		</div>
		<?php if (isset($category)): ?>
			<input type="hidden" name="category_id" value="<?php echo $cat['product_category_id']; ?>">
		<?php endif; ?>
	</form>
</div>


<script>
	$("#form").validate();
</script>