<style type="text/css">
	
	#products_attribute_options_table {
    display: inline-block;
    margin: -10px 2px;
}
</style>
<div class="row">

<?php if(isset($attribute)) {
		$action = "Edit"; 
	
 //print_r($attribute);


?>
<h3 title="<?php echo $action; ?> Attribute Field" class="page-header"><?php echo $action; ?> Attribute Field<span class="pull-right"><a href="<?php echo site_url('/productattribute/all'); ?>" class="btn btn-primary  responsive-btn">View All Attributes</a>&nbsp;</span></h3>
<?php
	if (isset($msg) && $msg != "") {
		echo '<div class="alert alert-warning">'. $msg .'</div>';
	}
?>
 

	 <div id="dialog_new_product_attribute_formw" title="Add Item Attribute">

	 <div class="col-md-12">
	 
	 </div>
	<form method="post" id="new_product_attribute_form">
		<div class="form-group">
			
			<div class="col-md-5">
			<label>Attribute Name:</label>
			<input type="text" name="attribute_name" value="<?php echo (isset($attribute)?$attribute->name:'');?>" class="form-control"  required/>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-12"><label>Unit of Measure (if applicable):</label> </div>
			<div class="col-md-5">
			
				<select name="unit_of_measure_id" class="form-control" id="unit_of_measure_id_dropdown" required>
					<option value="0">None</option>
					<?php foreach ($units as $key => $unit) {
					?>
						<option value="<?php echo $unit['unit_of_measure_id']; ?>"   <?php echo ( $unit['unit_of_measure_id']==$attribute->unit_of_measure_id) ?'selected':''?>  > <?php echo $unit['unit_of_measure_id']; ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-md-3">

					<a class="" id="add_new_unit_button"><i class="fa fa-plus" aria-hidden="true"></i> Add New Unit</a>
			 
			</div>
		</div>
		<div class="form-group">
		 <div class="col-md-12"><label>Options:</label> </div>
			 
			<!-- product attribute options table starts here -->

			 <div class="">
			<div class="col-md-4">
				<table id="products_attribute_options_table">
					<thead>
						<tr>
							<th>Option</th>
							<th>SKU Suffix</th>
						</tr>
					</thead>
					<tbody>
								<?php

		if(count($options) > 0) {
			foreach ($options as $option) {
				?>

						<tr>
							<td width="50%">
								<?php echo $option['name']; ?>
							</td>
							<td width="50%">
								<?php echo $option['sku_suffix']; ?>
							</td>
 						<td><a class="remove-attr-popup btn" onclick="remove_row_edit($(this),<?php echo $option['id']; ?>);"><i class="fa fa-trash-o" aria-hidden="true"></i> </a></td>


				<?php
			}

		}
		?>
					</tbody>
				</table>
				</div>
				<div class="col-md-4">
				<a class="" id="add_attribute_option_button"  ><i class="fa fa-plus" aria-hidden="true"></i> Add New Option</a>
					<div id="remove-option-id"> </div>
				 
			</div>
		 	</div>
			<!-- product attribute options table ends here -->
		</div>
		
		<?php 
		if (isset($attribute)) { ?>
			<input type="hidden" name="attribute_id" value="<?php echo $attribute->id; ?>" />
		<?php } ?>
	
		<div class="form-group ">

	<div class="col-md-12 top30"><button id="add-new-attribute" type="button" class="ui-button ui-corner-all ui-widget">Submit</button> </div>
</div>
	</form>


</div>



<?php 
} else {
		$action = "Add";
		?>


	 <div id="dialog_new_product_attribute_formw" title="Add Attribute Field">

	 <div class="col-md-12">
	 <h3 class="page-header">Add Attribute Field</h3>
	 </div>
	<form method="post" id="new_product_attribute_form">
		<div class="form-group">
			
			<div class="col-md-5">
			<label>Attribute Name:</label>
			<input type="text" name="attribute_name" class="form-control"  required/>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-12"><label>Unit of Measure (if applicable):</label> </div>
			<div class="col-md-5">
			
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
		 <div class="col-md-12"><label>Options:</label> </div>
			 
			<!-- product attribute options table starts here -->
			<div class="col-md-12">
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
				
			

				<a class="" id="add_attribute_option_button"  ><i class="fa fa-plus" aria-hidden="true"></i> Add New Unit</a>
				 
			</div>
		 
			<!-- product attribute options table ends here -->
		</div>

		<div class="form-group ">

	<div class="col-md-12 top30"><button id="add-new-attribute" type="button" class="ui-button ui-corner-all ui-widget">Submit</button> </div>
</div>
	</form>


</div>

	<?php } ?>

<script type="text/javascript">
	

 
$(document).ready(function(){

$('#add-new-attribute').on('click',function(){

			var paramObj = $('#new_product_attribute_form').serializeObject();


			  	if($("#new_product_attribute_form").valid()){
					$.ajax({
						url : appPath + "/productattribute/save_product_attribute",
					  	type: 'POST',
						data: paramObj,
						dataType: 'json',
						success:function(data){
							console.log(data)
							// adding the new option to the new attribute option dropdown for product
							$('#new_attribute_values').append('<option data-unit="'+data.unit+'" value="'+data.id+'">'+data.name+'</option>');
							// getting that option selected
							$('#new_attribute_values').val(data.id);
							// enabling the add product attribute button
							$('#add_new_product_attribute_button').prop('disabled', false);
							$('#dialog_new_product_attribute_form').dialog("close");
							$('#new_product_attribute_form')[0].reset();

						   location.href= appPath+'/productattribute/all';
						}
					});
				}

}) 

	// add new unit dialog starts here
	var new_unit_form = $('#new_unit_form').dialog({
		autoOpen: false,
	    resizable: true,
	    width:300,
	    modal: true,
        dialogClass: "ui-modal-wrapper",
		buttons:{
			"Add Unit":function(){
				var unit_name = $('input[name="unit_name"]').val();
			  	if($("#new_unit_form").valid()){
			  		$('#unit_of_measure_id_dropdown').append('<option value="'+unit_name+'" selected>'+unit_name+'</option>');
			  		$('#new_unit_form')[0].reset();
			  		$(this).dialog("close");
				}
			},
			"Cancel":function(){
				$('#new_unit_form')[0].reset();
				$(this).dialog("close");
			}
		}
	});
	$('#add_new_unit_button').click(function(){
		new_unit_form.dialog('open');
	});

	 $('#add_attribute_option_button').click(function(){
		$('#grey_box_attribute').val($('#new_product_attribute_form input[name="attribute_name"]').val());
		dialog_new_attribute_option_form.dialog('open');
	});
	// add new unit dialog ends here

	// add new product attribute dialog starts here
    //Modification: Modal skin problem fix
	var dialog_new_attribute_option_form = $('#new_attribute_option_form').dialog({
		autoOpen: false,
	    resizable: true,
	    width:300,
	    modal: true,
        dialogClass: "ui-modal-wrapper",
		buttons:{
			"Submit":function(){
				var form_data = {
		      		option_name: $('input[name="option_name"]').val(),
		      		sku_suffix: $('input[name="sku_suffix"]').val().toUpperCase(),
				};

 

			  	if($("#new_attribute_option_form").valid()){
			  		$('#products_attribute_options_table tbody').append('<tr class="product_attribute_row"><td><input type="hidden" name="attribute_option_name[]" value="'+form_data.option_name+'" />'+form_data.option_name+'</td><td><input type="hidden" name="attribute_option_suffix[]" value="'+form_data.sku_suffix+'" />'+form_data.sku_suffix+'</td><td><a class="remove-attr-popup btn" onclick="remove_row($(this));"><i class="fa fa-trash-o" aria-hidden="true"></i> </a></td></tr>');
			  		$('#new_attribute_option_form')[0].reset();
			  		$(this).dialog("close");
				}
			},
			"Cancel":function(){
				$('#new_attribute_option_form')[0].reset();
				$(this).dialog("close");
			}
		}
	});
	$('#add_attribute_option_button').click(function(){
		$('#grey_box_attribute').val($('#new_product_attribute_form input[name="attribute_name"]').val());
		dialog_new_attribute_option_form.dialog('open');
	});
	// add new product attribute dialog ends here
})
// jquery dialogs and actions for add/edit product page ends here

function remove_row_edit(obj,id){

	obj.parent().parent().remove();
	//console.log(obj);
	$('#remove-option-id').append("<input type='hidden' name='remove-option[]' value='"+id+"' />");

}
function remove_row(obj){

	obj.parent().parent().remove();
//	console.log(obj);

}

// form data to json
$.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

</script>
<div id="dialog_new_unit_form" title="Add New Unit">
	<form method="post" id="new_unit_form">
		<div class="form-group">
			<label>Unit Name:</label>
			<input type="text" name="unit_name" class="form-control"  required/>
		</div>
	</form>
</div>

<div id="dialog_new_attribute_option_form" title="Add New Unit">
	<form method="post" id="new_attribute_option_form">
		<div class="form-group">
			<label>Attribute:</label>
			<input type="text" id="grey_box_attribute" class="form-control" readonly />
		</div>
		<div class="form-group">
			<label>Option Name:</label>
			<input type="text" name="option_name" class="form-control"  required/>
		</div>
		<div class="form-group">
			<label>SKU Suffix (if applicable):</label>
			<input type="text" name="sku_suffix" class="form-control" style="text-transform:uppercase"/>
		</div>
	</form>
</div>


