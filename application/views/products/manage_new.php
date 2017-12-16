<style type="text/css">
.qq-gallery .qq-upload-button {
    display: inline;
    width: 155px;
    padding: 7px 10px;
    float: left;
    text-align: center;
    background: transparent;
    color: #000;
    border-radius: 0px;
    border: none;
    box-shadow: none;
}
</style>
 
<script src="<?php echo base_url(); ?>assets/fine-uploader/fine-uploader.js"></script>

<?php 
	// view for dialog for adding product category 
	$this->load->view('common/product_category_form');

	// view for dialog for adding product images 
	//$this->load->view('common/upload_product_images_form');

	// view for dialog for adding product attributes
	$this->load->view('common/new_product_attribute_form');

	// view for dialog for adding new unit of measure
	$this->load->view('common/new_unit_form');

	// view for dialog for adding new product attribute option 
	$this->load->view('common/new_attribute_option_form');

	if($product) { 
		$type = $product->product_type;

		//$type = 'Item';
		$action = "Edit"; 
	} else {
		$action = "Add";
	}
	 
	if(isset($type)==false)
	{
		$type = 'Item';
	}
?>

<div class="row">
	<h3 class="page-header">
		<?php echo $action; ?>  Item <span class="pull-right">
		<a href="<?php echo site_url('products/all'); ?>" class="btn btn-primary">View All Items</a>&nbsp;</span>
	</h3>
	<?php if (isset($msg) && $msg != ""): ?>
		<div class="alert alert-warning"><?php echo $msg; ?></div>
	<?php endif; ?>

		<div class="col-md-12">
		<div class="col-md-9">
		<form method="post" id="form">
			<section class="section-variant">
				<div class="form-group">
					<label>Name:</label>
					<input type="text" name="name" class="form-control" value="<?php echo $product ? $product->name : '';?>" required />
				</div>
				<div class="form-group row">
					<div class="col-sm-9">
						<div>
							<label>Item type:</label>
							<select name="product_type" class="form-control" id="type_select" required="" data-postrequisite="#item_category_select">
								<option value="">Select Item Type</option>
								<option value="product"  <?php echo( ( $product && $product->product_type == 'Product') ||isset($type)==false )?'selected':''?>>Product</option>
								<option value="equipment"  <?php echo( $product && $product->product_type == 'Equipment')?'selected':''?>>Equipment</option>
								<option value="part"  <?php echo( $product && $product->product_type == 'Part')?'selected':''?>>Part</option>
								<option value="material"  <?php echo( $product && $product->product_type == 'Material')?'selected':''?>>Material</option>
							</select>
						</div>
					</div>
					<div class="col-sm-3">
					</div>
				</div>
				
				<div class="form-group row">
					<div class="col-sm-9">
						<div>
							<label>Category:</label>
							<select name="category" class="form-control" id="item_category_select" required="" 
								data-value="<?php if ($product) print $product->product_category_id; ?>" 
								data-prerequisite="#type_select" data-shadow="1">
								<option value="">Select Category</option>
								<?php foreach ($product_categories as $product_category): ?>
									<option value="<?php echo $product_category['product_category_id']; ?>" <?php echo( isset($product->product_category_id) && $product->product_category_id == $product_category['product_category_id'])?'selected':''?>  ><?php echo $product_category['name']; ?></option>
								<?php endforeach; ?>
							</select>
							<select class="donotsort" id="item_category_select_shadow" style="display: none" disabled>
								<?php foreach ($product_categories as $product_category): ?>
									<option value="<?php echo $product_category['product_category_id']; ?>" data-filter="<?php print $product_category['item_category_type']; ?>"><?php echo $product_category['name']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="col-sm-3">
					</div>
				</div>
				
				<div class="form-group">
					<label>Owner:</label>
					<select name="party_id" class="form-control" id="product_owner" required="">
						<option value="">Select Owner</option>
						<?php if(count($parties)){
						foreach ($parties as $party) { ?>
						<option value="<?php echo $party['id']; ?>"  <?php echo( isset($product->party_type_allocation_id) && $product->party_type_allocation_id == $party['id'])?'selected':''?>><?php echo $party['display_name']; ?></option>
						<?php 	}
						}	?>
					</select>
				</div>
				
				<div class="form-group">
					<label>Desciption:</label>
					<textarea name="description" class="form-control" required><?php if ($product) print $product->description; ?></textarea>
				</div>
				
				<div class="form-group">
					<label>Images:</label>
					<input type="hidden" name="image_directory" value="<?php echo $this->session->userdata('product_directory'); ?>" />
					<div id="fine-uploader-gallery"></div>
					<div id="product_images"> </div>
					
				</div>
			</section>
			
			<section class="section-variant" id="section-variantss">
				<div><label>Identification and Variants 	</label> </div>
				
				<div class="form-group row">
					<div class="col-sm-8">
						<label>Base SKU:</label>
						<input required  type="text" name="base_sku" class="form-control" value="<?php echo (isset($product->sku)?$product->sku:'');?>" id="base_sku_text"/>
					</div>
					<div class="col-sm-4">
						<a class="help-icon help-icon3 "  data-html="false"  data-toggle="popover" title="Hint" data-content="
							This needs to be a unique string for Base SKU."  >
						<i class="fa fa-question-circle" aria-hidden="true"></i></a>
					</div>
				</div>
				
				<div class="section-label"><label>Attributes and Variants:</label>  </div>
				<div class="">
					<div class="form-group">
						<select id="new_attribute_values" class="form-control">
							<option value="0">Select Attribute</option>
							<?php foreach ($attributes as $attribute) { ?>
							<option data-unit="<?php echo $attribute['unit_of_measure_id']; ?>" value="<?php echo $attribute['id']; ?>"><?php echo $attribute['name'];?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-7">
						<div class="form-group row" id="section-variant">
							<div class="col-sm-5">
								<span>Option Name</span>
							</div>
							<div class="col-sm-5">
								<span>Option Value</span>
							</div>
							<div class="col-sm-2">
								<span>Action</span>
							</div>
						</div>
					</div>
					<div class="col-sm-5">
						<div  id="variant_box">
							<table id="products_variants_table">
								<thead>
									<tr>
										<th>Variants</th>
										<th>SKU Suffix</th>
										<th style="position:relative;">Status</th>
									</tr>
								</thead>
								<tbody>
									
								</tbody>
							</table>
						</div>
					</div>
				</div>
				
			</section>
			
			<!-- product attributes and variants starts here -->
			<div class="form-group row hidden">
				<div class="col-sm-4">
					<label>Attributes and Variants:</label>
				</div>
				<div class="col-sm-4">
					<a class="" id="new_product_attribute_button"><i class="fa fa-plus" aria-hidden="true"></i> Add New Attribute</a>
				</div>
				<div class="col-sm-4">
				</div>
			</div>
			
			<!-- product attributes and variants ends here -->
			<div class="clearfix"></div>
			
			<section class="section-variant">
				<div class="form-group row">
					<div class="col-sm-8 col-md-6">
						<label>Status:</label>
						<select name="status" class="form-control" id="product_status_main_select" required="">
							<option value="R&D">R&D</option>
							<option value="Active" selected>Active</option>
							<option value="Discontinued">Discontinued</option>
							<option value="End of Line">End of Line</option>
						</select>
					</div>
					<div class="col-md-4">
						<a class="help-icon help-icon2 help_id"  data-html="false"  data-toggle="popover" title="Hint" data-content="Item will only be able to be made inactive once all warehouse and kiosk stock has been disposed of." ><i class="fa fa-question-circle" aria-hidden="true"></i></a>
					</div>
					<div class="col-sm-2 col-md-4">
					</div>
				</div>
				
				<div class="form-group">
					<input type="submit" class="btn btn-primary" value="Submit" name="save" />&nbsp;<a href="<?php echo site_url('products/all'); ?>" class="btn ">Cancel</a>
				</div>
				
				<?php
				if (isset($product)) { ?>
				<input type="hidden" name="product_id" value="<?php if ($product) print $product->id; ?>" />
				<?php } ?>
			</section>
		</form>
	</div>
	</div>

	
	<script>
		$("#form").validate();
	</script>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>

	<!-- javascript for making changes in the current page starts here-->
	<script type="text/javascript">


	var attributes_options_selected = <?php echo json_encode($attributes_options_selected) ?>;
 

		// check on selection of new attribute adding to the box
		$('#new_attribute_values').change(function(){

			var attribute = {};
			attribute.id = $('#new_attribute_values').val();
			attribute.name= $('#new_attribute_values option:selected').html();
			attribute.unit = $('#new_attribute_values option:selected').data('unit');
			// if it is a valid option then only make the "add product attribute" button accessible
			if($(this).val() != 0){
				$('#add_new_product_attribute_button').prop('disabled', false);
				 $('.class_attr_'+attribute.id).remove();
				new AddProductAttribute(attribute);
			}else{ // disabling button on invalid attribute selection
				$('#add_new_product_attribute_button').prop('disabled', true);
			}
		});

		// creating options when a valid selection is made in add attribute dropdown
	    var AddProductAttribute  = function(attribute){


	    	 
			var new_attribute_id = attribute.id ;
		 
		     
			var new_attribute_label = attribute.name;
			var unit =  attribute.unit;

			// do not show unit when unit == "0"
			if(unit == '0')
				unit = '';

			// fetching options for the selected attribute
			$.ajax({
					url : appPath + "/productattribute/get_product_attribute_options",
				  	type: 'POST',
					data: {'id': new_attribute_id},
					dataType: 'json',
					success:function(data){
						// if there are many options then we show as multiselect
						//console.log(data);
						if(data.length > 0){
							$('#attribute_name_box').append('<div class="form-group" id="multi_product_option_'+new_attribute_id+'"><label>'+new_attribute_label+'</label></div>');
							// variable for selecting the created option box
							var attrib_box_selector = '#multi_product_option_'+new_attribute_id;
							// creating the multiple checkboxes for option values

							var options = '';
							for(i in data){
								$('#attribute_name_box '+attrib_box_selector+' select').append('');


									options +=	'<option data-sku_suffix="'+data[i].sku_suffix+'" data-attribute_id="'+new_attribute_id+'" value="'+data[i].id+'">'+data[i].name+'</option>';
							}

							// making it multiselect with plugin call
							 



							  var tmpl = ' <div class="form-group row class_attr class_attr_'+new_attribute_id+'">'+

												'<div class="col-sm-5">'+
												    '<span>'+new_attribute_label+'</span>'+
												'</div>'+
												'<div class="col-sm-5">'+

								 				'<select id="attribute-option-'+new_attribute_id+'" class="selectpicker" name="attribute-option['+new_attribute_id+'][]"  multiple title="Select '+new_attribute_label+' Options" data-container="body" data-actions-box="false" data-showTick="true" data-width="100%" data-selected-text-format="count > 4">'+
								 					options+
								 					'</select>'+

												'</div>'+
												'<div class="col-sm-2">'+
													 '<span> <a class="remove-attr btn"><i class="fa fa-trash-o" aria-hidden="true"></i></a></span>'+
												'</div>'+

												
												 '</div>'+

							                '</div>'
							$('#section-variant').after(tmpl);     

							 $('#attribute-option-'+new_attribute_id).selectpicker({
							 	countSelectedText: function(num) { 
												      return "{0} "+new_attribute_label+" selected";
												   }
							 });
							  // method to be called when any multiselect value has changed


				 	
						}else{ 

							 
							// else show as the textbox
							$('#attribute_name_box').append('<div class="form-group"><label>'+new_attribute_label+'</label><div class="input-group"><input type="text" class="form-control" name="attribute-option['+new_attribute_id+']"/><span class="input-group-addon">'+unit+'</span></div></div>');


							var tmpl = ' <div class="form-group row class_attr class_attr_'+new_attribute_id+'">'+

											'<div class="col-sm-5">'+
											    '<span>'+new_attribute_label+'</span>'+
											'</div>'+
											'<div class="col-sm-5">'+

							 				'<div class="input-group"><input type="text" class="form-control" name="attribute-option['+new_attribute_id+']"/><span class="input-group-addon">'+unit+'</span></div>'+

											'</div>'+
											'<div class="col-sm-2">'+
												 '<span> <a class="remove-attr btn"><i class="fa fa-trash-o" aria-hidden="true"></i></a></span>'+
											'</div>'+

											 
											 '</div>'+

										'</div>'
									$('#section-variant').after(tmpl);     
						}

						


						// removing the selected option from the dropdown and making them select again for new
						$("#new_attribute_values option[value='"+new_attribute_id+"']").remove();
						$("#new_attribute_values").val('0');
						// disabling button on invalid attribute selection
						$('#add_new_product_attribute_button').prop('disabled', true);
					}
			});
	    }

		$(document.body).on('changed.bs.select','.selectpicker', function (e, clickedIndex, newValue, oldValue) {
 
					 
							// getting values from the selected option
							var sku_suffix = $(e.target[clickedIndex]).data('sku_suffix');
							var option_value = $(e.target[clickedIndex]).val();
							var option_text = $(e.target[clickedIndex]).text();
							var attribute_id = $(e.target[clickedIndex]).data('attribute_id');
							// if the option is selected then add a variant div in the variant box
							if(newValue){
								// add a new row in the #products_variants_table with required variables
								$('#products_variants_table tbody').append('<tr id="'+option_value+'_variant_box" class="variant_divs"><td>'+option_text+'<input type="hidden" name="variant_attribute_id[]" value="'+attribute_id+'"/></td><td>'+sku_suffix+'<input type="hidden" name="variant_sku_suffix[]" value="'+sku_suffix+'"/><input type="hidden" name="variant_name[]" value="'+option_text+'"/></td><td><input type="hidden" name="variant_attribute_option_id[]" value="'+option_value+'"/><select id="variant_status_'+option_value+'" name="variant_status[]" class="form-control"></select></td></tr>');
								// adding options to the variant status dropdown
								$('#variant_status_'+option_value).append($("#product_status_main_select > option").clone());
								// show variant box
								$('#variant_box').show();
							}else{ // remove variant div for the corresponding option
								$('#'+option_value+'_variant_box').remove();
								// hide variant box if it is empty
								if($('#products_variants_table tbody tr').length < 1){
									$('#variant_box').hide();
								}
							}
		 });

$('#add_new_product_attribute_button').click(function(){
		var new_attribute_id = $('#new_attribute_values').val();
		var new_attribute_label = $('#new_attribute_values option:selected').text();
		var unit = $('#new_attribute_values option:selected').data('unit');
		// do not show unit when unit == "0"
		if(unit == '0')
			unit = '';
		// fetching options for the selected attribute
		$.ajax({
				url : appPath + "/productattribute/get_product_attribute_options",
			  	type: 'POST',
				data: {'id': new_attribute_id},
				dataType: 'json',
				success:function(data){
					// if there are many options then we show as multiselect
					if(data.length > 0){
						$('#attribute_name_box').append('<div class="form-group" id="multi_product_option_'+new_attribute_id+'"><label>'+new_attribute_label+'</label><select id="attribute-option-'+new_attribute_id+'" class="selectpicker" name="attribute-option['+new_attribute_id+'][]"  multiple title="Select '+new_attribute_label+' Options" data-container="body" data-actions-box="false" data-showTick="true" data-width="100%" data-selected-text-format="count > 4"></select></div>');
						// variable for selecting the created option box
						var attrib_box_selector = '#multi_product_option_'+new_attribute_id;
						// creating the multiple checkboxes for option values
						for(i in data){
							$('#attribute_name_box '+attrib_box_selector+' select').append('<option data-sku_suffix="'+data[i].sku_suffix+'" data-attribute_id="'+new_attribute_id+'" value="'+data[i].id+'">'+data[i].name+'</option>');
						}
						// making it multiselect with plugin call
						 $('#attribute-option-'+new_attribute_id).selectpicker({
						 	countSelectedText: function(num) { 
											      return "{0} "+new_attribute_label+" selected";
											   }
						 });
						
					}else{ // else show as the textbox
						$('#attribute_name_box').append('<div class="form-group"><label>'+new_attribute_label+'</label><div class="input-group"><input type="text" class="form-control" name="attribute-option['+new_attribute_id+']"/><span class="input-group-addon">'+unit+'</span></div></div>');
					}
					// removing the selected option from the dropdown and making them select again for new
					$("#new_attribute_values option[value='"+new_attribute_id+"']").remove();
					$("#new_attribute_values").val('0');
					// disabling button on invalid attribute selection
					$('#add_new_product_attribute_button').prop('disabled', true);
				}
		});
	});



		// checking if the base sku entered is unique or not
		$('#base_sku_text').blur(function(){
			// value of sku in the form
			var sku = $('#base_sku_text').val();
			if(sku!=""){
			// firing ajax to check in the database
			$.ajax({
						url : appPath + "/products/check_for_sku",
					  	type: 'POST',
						data: {'sku': sku},
						dataType: 'json',
						success:function(data){
							// alert if the sku already exists
							if(data.result == 'true'){
								alert('Kindly enter another value for Base SKU. There is already a product with this SKU.');
								$('#base_sku_text').val('');
								$('#base_sku_text').focus();
							}
						}
			});

			}
		})
	</script>

	<!-- javascript for making changes in the current page ends here-->



	 <script type="text/template" id="qq-template-gallery">
	        <div class="qq-uploader-selector qq-uploader qq-gallery" qq-drop-area-text="Drop files here">
	            <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
	                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
	            </div>
	            <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
	                <span class="qq-upload-drop-area-text-selector"></span>
	            </div>
	            <div class="qq-upload-button-selector qq-upload-button">
	                <div>Upload Images</div>
	            </div>
	            <span class="qq-drop-processing-selector qq-drop-processing">
	                <span>Processing dropped files...</span>
	                <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
	            </span>
	            <ul class="qq-upload-list-selector qq-upload-list" role="region" aria-live="polite" aria-relevant="additions removals">
	                <li>
	                    <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
	                    <div class="qq-progress-bar-container-selector qq-progress-bar-container">
	                        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
	                    </div>
	                    <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
	                    <div class="qq-thumbnail-wrapper">
	                        <img class="qq-thumbnail-selector" qq-max-size="120" qq-server-scale>
	                    </div>
	                    <button type="button" class="qq-upload-cancel-selector qq-upload-cancel">X</button>
	                    <button type="button" class="qq-upload-retry-selector qq-upload-retry">
	                        <span class="qq-btn qq-retry-icon" aria-label="Retry"></span>
	                        Retry
	                    </button>

	                    <div class="qq-file-info">
	                        <div class="qq-file-name">
	                            <span class="qq-upload-file-selector qq-upload-file"></span>
	                            <span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
	                        </div>
	                        <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
	                        <span class="qq-upload-size-selector qq-upload-size"></span>
	                        <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">
	                            <span class="qq-btn qq-delete-icon" aria-label="Delete"></span>
	                        </button>
	                        <button type="button" class="qq-btn qq-upload-pause-selector qq-upload-pause">
	                            <span class="qq-btn qq-pause-icon" aria-label="Pause"></span>
	                        </button>
	                        <button type="button" class="qq-btn qq-upload-continue-selector qq-upload-continue">
	                            <span class="qq-btn qq-continue-icon" aria-label="Continue"></span>
	                        </button>
	                    </div>
	                </li>
	            </ul>

	            <dialog class="qq-alert-dialog-selector">
	                <div class="qq-dialog-message-selector"></div>
	                <div class="qq-dialog-buttons">
	                    <button type="button" class="qq-cancel-button-selector">Close</button>
	                </div>
	            </dialog>

	            <dialog class="qq-confirm-dialog-selector">
	                <div class="qq-dialog-message-selector"></div>
	                <div class="qq-dialog-buttons">
	                    <button type="button" class="qq-cancel-button-selector">No</button>
	                    <button type="button" class="qq-ok-button-selector">Yes</button>
	                </div>
	            </dialog>

	            <dialog class="qq-prompt-dialog-selector">
	                <div class="qq-dialog-message-selector"></div>
	                <input type="text">
	                <div class="qq-dialog-buttons">
	                    <button type="button" class="qq-cancel-button-selector">Cancel</button>
	                    <button type="button" class="qq-ok-button-selector">Ok</button>
	                </div>
	            </dialog>
	        </div>
	    </script>


	     <script>


	        var galleryUploader = new qq.FineUploader({
	            element: document.getElementById("fine-uploader-gallery"),
	            template: 'qq-template-gallery',
	            request: {
	                endpoint: appPath+'/Products/uploads'
	            },
	            thumbnails: {
	                placeholders: {
	                    waitingPath: window.location.origin+'/assets/fine-uploader/placeholders/waiting-generic.png',
	                    notAvailablePath: window.location.origin
	+'/assets/fine-uploader/placeholders/not_available-generic.png'
	                }
	            },
	            validation: {
	                allowedExtensions: ['jpeg', 'jpg', 'gif', 'png']
	            },
	            chunking: {
	                enabled: true
	            },
	            resume: {
	                enabled: true
	            },
	            deleteFile: {
	                enabled: true,
	                method: "POST",
	                endpoint: appPath+'/Products/uploads'
	                
				},
				callbacks: {
						    onDelete: function(id) {
						     

						    },
						    onDeleteComplete: function(id, xhr, isError) {
						     	
						     	$('#image'+id).remove();

						    },
						     onComplete: function(id, fileName, responseJSON) {
						     	console.log(responseJSON);
						     	console.log(id);
						     	console.log('li[qq-file-id='+id+']');

						     	$('li[qq-file-id='+id+']').find('.qq-upload-delete').attr('data-deleteUrl',responseJSON.files[0].deleteUrl);

						     	$('#product_images').append('<input type="hidden" value="'+responseJSON.files[0].name+'" name="images['+id+']" id="image'+id+'" /> ');
						     }  
				 }
	        });

	      

	      		$(document.body).on('click','.remove-attr',function(){

	        	$(this).parent().parent().parent().remove();

	        });

	        $(document).ready(function() {

	   	       $('#item_category_select').on('change', function() {
		       		 
					 App.getattribute(this.value);
				})

				 $.each(attributes_options_selected, function(i, obj) {
				  
				 	 $('#new_attribute_values option[value="'+i+'"]').prop('selected','selected').change();
				 	

				 	 setTimeout(function(){
				 	 var count = Object.keys(obj).length;
 				   	
				  	 
				 	 if(obj.id){
				 	 	 $('input[name="attribute-option['+obj.product_attribute_id+']"]').val(obj.value);
				 	 	 console.log('input[name="attribute-option['+obj.product_attribute_id+']"]');
				 	 }else{

				 	 	console.log("multiple")
				 	 	 $.each(obj, function(j, obj1) {
				 	 		$('#attribute-option-'+obj1.product_attribute_id+' option[value="'+obj1.product_attribute_option_id+'"]').attr("selected","selected");$('#attribute-option-'+obj1.product_attribute_id+'').change()
				 		  });
				 	 }


				 	 console.log(obj)

				 	 },1000);

				 	 //alert(11)
				 });



	        


		      $('[data-toggle="popover"]').popover({ trigger: "hover" });	

			  $('textarea').summernote();

			  $('.qq-upload-list').append($('#product-images-edit').html());

			  $('.qq-upload-delete-selector-update').on('click',function(){

			  	var id = '-'+$(this).data('id');
			  	var did =  $(this).data('id');
			  	var deleteUrl = $(this).data('deleteurl');
				  $.ajax({
					url : appPath+'/products/deleteimage',
				  	type: 'POST',
					data: {'id': $(this).data('id')},
					dataType: 'json',
						success:function(data){
							if(data.result){
								 
							 $('li[qq-file-id='+did+']').remove();;
							}else{
								alert("Failed to delete!! Try again");
							}
						}

					});

			  });
			});

	    </script>


<div id="product-images-edit"  style="display: none;">
<?php

 


if( isset($images[0]) && count($images[0])>0){
foreach ($images as $key => $image) {

?>
<li class="qq-file-id--<?php echo $image->id?> qq-upload-success" qq-file-id="-<?php echo $image->id?>">
	                    <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
	                    <div class="qq-progress-bar-container-selector qq-progress-bar-container qq-hide">
	                        <div role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar" style="width: 100%;"></div>
	                    </div>
	                    <span class="qq-upload-spinner-selector qq-upload-spinner qq-hide"></span>
	                    <div class="qq-thumbnail-wrapper">
	                        <img class="qq-thumbnail-selector" qq-max-size="120" qq-server-scale="" src="<?php echo "/uploads/products/".$product->image_directory.'/thumbnail/'.$image->location?>">
	                    </div>
	                    <button type="button" class="qq-upload-cancel-selector qq-upload-cancel qq-hide">X</button>
	                    <button type="button" class="qq-upload-retry-selector qq-upload-retry qq-hide">
	                        <span class="qq-btn qq-retry-icon" aria-label="Retry"></span>
	                        Retry
	                    </button>

	                    <div class="qq-file-info">
	                        <div class="qq-file-name">
	                            <span class="qq-upload-file-selector qq-upload-file" title="<?php echo $image->location?>"><?php echo $image->location?></span>
	                            <span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
	                        </div>
	                        <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
	                        <span class="qq-upload-size-selector qq-upload-size"> </span>
	                        <button type="button" class="qq-btn qq-upload-delete-selector-update qq-upload-delete" data-id="-<?php echo $image->id?>" data-deleteurl="/products/uploads/?file=<?php echo $image->location?>">
	                            <span class="qq-btn qq-delete-icon" aria-label="Delete"></span>
	                        </button>
	                        <button type="button" class="qq-btn qq-upload-pause-selector qq-upload-pause qq-hide">
	                            <span class="qq-btn qq-pause-icon" aria-label="Pause"></span>
	                        </button>
	                        <button type="button" class="qq-btn qq-upload-continue-selector qq-upload-continue qq-hide">
	                            <span class="qq-btn qq-continue-icon" aria-label="Continue"></span>
	                        </button>
	                    </div>
	                </li>

	          <?php } }    ?>

	                </div>

<div id="product-variants-block"  style="display: block;">
							<?php 

								foreach ($attributes_options as $key => $option) {  
									  if ( isset($attributes_options_selected[1]) &&array_key_exists($option['id'],$attributes_options_selected[1])) { ?>
						 <script>

											 
							var sku_suffix = '<?php echo $option['sku_suffix']?>';
							var option_value = '<?php echo $option['id']?>';
							var option_text = '<?php echo $option['name']?>';
							var attribute_id =  '<?php echo $option['product_attribute_id']?>';
							var objId = '<?php echo $product->sku ?>-'+sku_suffix;

							// if the option is selected then add a variant div in the variant box
							 
								// add a new row in the #products_variants_table with required variables
								$('#products_variants_table tbody').append('<tr id="'+option_value+'_variant_box" class="variant_divs"><td>'+option_text+'<input type="hidden" name="variant_attribute_id[]" value="'+attribute_id+'"/></td><td>'+sku_suffix+'<input type="hidden" name="variant_sku_suffix[]" value="'+sku_suffix+'"/><input type="hidden" name="variant_name[]" value="'+option_text+'"/></td><td><input type="hidden" name="variant_attribute_option_id[]" value="'+option_value+'"/><select  id="variant_status_'+option_value+'" name="variant_status[]" class="form-control '+objId+'"></select></td></tr>');
								// adding options to the variant status dropdown
								$('#variant_status_'+option_value).append($("#product_status_main_select > option").clone());
								// show variant box
								$('#variant_box').show();		
									

						 </script>
									
				   
									 	<?php  }

									 	} ?>

<script type="text/javascript">
	<?php	foreach ($allsku as $key => $ssku) {  ?>
		var ssku = '<?php echo $ssku['sku_value'] ?>';

		var status = '<?php echo $ssku['status'] ?>';

		$('.'+ssku+' option[value="'+status+'"]').attr('selected','selected');

	<?php } ?>
</script>

</div>

<div id="product-variants-block-charge"  style="display: none;">
							<?php 
									  
									foreach ($attributes_charge_statues as $key => $option) {  
										 
 
									  if ( isset($attributes_options_selected[7]) &&array_key_exists($option['id'],$attributes_options_selected[7])) { ?>
						 <script>

											 
							var sku_suffix = '<?php echo $option['sku_suffix']?>';
							var option_value = '<?php echo $option['id']?>';
							var option_text = '<?php echo $option['name']?>';
							var attribute_id =  '<?php echo $option['product_attribute_id']?>';
							// if the option is selected then add a variant div in the variant box
							 
								// add a new row in the #products_variants_table with required variables
								$('#products_variants_table tbody').append('<tr id="'+option_value+'_variant_box" class="variant_divs"><td>'+option_text+'<input type="hidden" name="variant_attribute_id[]" value="'+attribute_id+'"/></td><td>'+sku_suffix+'<input type="hidden" name="variant_sku_suffix[]" value="'+sku_suffix+'"/><input type="hidden" name="variant_name[]" value="'+option_text+'"/></td><td><input type="hidden" name="variant_attribute_option_id[]" value="'+option_value+'"/><select id="variant_status_'+option_value+'" name="variant_status[]" class="form-control"></select></td></tr>');
								// adding options to the variant status dropdown
								$('#variant_status_'+option_value).append($("#product_status_main_select > option").clone());
								// show variant box
								$('#variant_box').show();		
									

						 </script>
									
				   
									 	<?php  }

									 	} ?>

</div>
