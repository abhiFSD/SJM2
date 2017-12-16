<div id="dialog_new_product_attribute_form" title="Add Item Attribute">
	<form method="post" id="new_product_attribute_form">
		<div class="form-group">
			
			<div class="col-md-12">
			<label>Attribute Name:</label>
			<input type="text" name="attribute_name" class="form-control"  required/>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-12"><label>Unit of Measure (if applicable):</label> </div>
			<div class="col-md-9">
			
				<select name="unit_of_measure_id" class="form-control" id="unit_of_measure_id_dropdown" required>
					<option value="0">None</option>
					<?php foreach ($units as $key => $unit) {?>
						<option value="<?php echo $unit['unit_of_measure_id']; ?>"><?php echo $unit['unit_of_measure_id']; ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-md-3">

			<a class="" id="add_new_unit_button"><i class="fa fa-plus" aria-hidden="true"></i> Add New Unit</a>
			 
			</div>
		</div>
		<div class="form-group">
		<div class="col-md-12">
			<label>Options:</label>
			<!-- product attribute options table starts here -->
			<div class="text-centre">
				<table id="products_attribute_options_table">
					<thead>
						<tr>
							<th>Option</th>
							<th>SKU Suffix</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
				<button class="btn btn-primary" id="add_attribute_option_button" type="button">Add Option</button>
			</div>
			</div>
			<!-- product attribute options table ends here -->
		</div>
	</form>
</div>