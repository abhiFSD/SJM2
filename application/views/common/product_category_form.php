<div id="dialog_product_category_form" title="Add Item Category">
	<form method="post" id="product_category_form">
		<div class="form-group">
			<label>Category Name:</label>
			<input type="text" name="category_name" class="form-control"  required/>
		</div>
		<div class="form-group">
			<label>Category Name Abbreviation:</label>
			<input type="text" name="name_abbreviation" class="form-control"  required/>
		</div>
		<div class="form-group">
			<label>Parent Category:</label>
			<select name="parent_product_category_id" class="form-control" id="parent_product_category_id" required>
				<option value="0">None</option>
				<?php foreach ($product_categories as $product_category) { ?>
					<option value="<?php echo $product_category['product_category_id']; ?>"><?php echo $product_category['name']; ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="form-group">
			<label>Status:</label>
			<select name="status" id="category_status" class="form-control" required>
				<option value="1">Active</option>
				<option value="0">Inactive</option>
			</select>
		</div>
		<input type="hidden" name="name_abbreviation"  value="<?php //echo $license_id; ?>"  />
	</form>
</div>