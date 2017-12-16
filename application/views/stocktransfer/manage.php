<?php 
$stage = $transfer->status;
$date =  new DateTime($transfer->estimated_delivery);

$dateValue = $transfer->estimated_delivery;

?>
<div class="row">
<div class="col-md-12">
<form action="" method="post">
	<h2>Stock Transfer Form</h2>
	<div class="row alert alert-warning">
		<div class="col-md-6">
		<?php 
	                $property = "";
	                if ($stage != "Save As Draft" ) {
	               		$property = "readonly";
	               	}
					
	               	if ($stage == "Dispatched" || $stage == "Received") {
	               		$propertyRd = "readonly";
	               	}
	               	
		?>
	    <div class="form-group">
	        <label>From: *</label>
	        <select id="from_location" name="from_location" onchange="" class="form-control" <?php echo $property; ?>>
	        <option value="">Select Location</option>
	                <?php 
		                foreach ($locations as $location ) { 
								$selected = '';
								if ($location['id'] == $transfer->from_location_id) {
									$selected = 'selected="selected"';
								}
							?>
		                    <option value="<?php echo $location['id'] ?>" <?php echo $selected; ?>><?php echo $location['name'] ?></option>
		                <?php } 
					?>
	                </select>
	        
	   </div>
	   <div class="form-group">
	        <label>To: *</label>
	         <select id="to_location" name="to_location" onchange="" class="form-control" <?php echo $property; ?>>
	                    <option value="">Select Location</option>
	                <?php foreach($locations as $location ) { 
	                	$selected = '';
	                	if ($location['id'] == $transfer->to_location_id) {
	                		$selected = 'selected="selected"';
	                	}
	                	?>
	                    <option value="<?php echo $location['id'] ?>" <?php echo $selected; ?>><?php echo $location['name'] ?></option>
	                <?php } ?>
	                
	                
	       </select>
	   </div>
	     <?php 
		   	if ($stage == "Picked and Packed" || $stage == "In Transit"    ) { 

				$property = '';
				$time[0] = $time[1] = "";
				if ($stage == "In Transit" || $stage == "Received") {
					if ($transfer->estimated_time != "") {
						$time = explode("-", $transfer->estimated_time);
					}
						
					$property = "readonly";
				}
			?>
	   		<div class="form-group">
		        <label>Estimated Delivery:</label>
		        
		        <input type="date" name="est_delivery_date"   value="<?php echo $dateValue; ?>" class="form-control"  <?php echo $property; ?> />
		   </div>
		   <div class="form-group">
		        <label>Estimated Time:</label>
		        <br />
		        Between <input type="text" name="time1" value="<?php echo $time[0]; ?>" class="form-control" <?php echo $property; ?> /> and <input type="text" name="time2" value="<?php echo $time[1]; ?>" class="form-control" <?php echo $property; ?> /> 
		   </div>
		   <div class="form-group">
		        <label>No. Of Cartons:</label>
		        
		        <input type="text" name="no_of_cartons"   value="<?php echo $transfer->number_of_cartons;?>" class="form-control"  <?php echo $property; ?> />
		   </div>
		   <div class="form-group">
	        <label>Freight Provider: *</label>
	         <select id="freight_location" name="freight_location" onchange="" class="form-control" value="<?php echo $transfer->freight_provider_id; ?>" <?php echo $property; ?>>
	                    <option value="">Select Location</option>
	                <?php foreach($freightLocations as $location ) { 
	                	$selected = '';
	                	if ($location['id'] == $transfer->freight_provider_id) {
	                		$selected = 'selected="selected"';
	                	}
	                	?>
	                	?>
	                    <option value="<?php echo $location['id'] ?>" <?php echo $selected; ?>><?php echo $location['name'] ?></option>
	                <?php } ?>
	       </select>
	   </div>	
	   
	   <?php } ?>
	 <!--  
	     -->
	   </div>
	   <div class="col-md-6">
	   
		   <div class="form-group">
		        <label>Current Status:</label>
		        
		        <input type="text" name="currentstatus"   value="<?php echo $transfer->status;?>" class="form-control" readonly />
		   </div>
		   <div class="form-group">
		        <label>Current Location:</label>
		        <input type="text" name="currentlocation" value="<?php echo $currentLocation->name; ?>" class="form-control" readonly />
		   </div>
		   <?php 
		   			$notes ='';
		   			if ($stage == "Picked and Packed" || $stage == "In Transit"  ) { 
						$property = '';
						$notes = 'To be picked up from Eagle Farm DHL Depot';
						if ($stage == 'In Transit' || $stage == "Received") {
							$property = "readonly";
							
						} 
					?>
		   
		   <div class="form-group">
		        <label>Tracking Number:</label>
		        
		        <input type="text" name="tracking_number"   value="<?php echo $transfer->tracking_number;?>" class="form-control" <?php echo $property; ?> />
		   </div>				
			<div class="form-group">
		        <label>Tracking Link:</label>
		        
		        <input type="text" name="tracking_link"   value="<?php echo $transfer->tracking_link;?>" class="form-control"  <?php echo $property; ?> />
		   </div>
		  
	       <?php   } ?>
	         <div class="form-group">
		        <label>Notes:</label>
		        <textarea name="notes" class="form-control" ><?php echo $notes; ?></textarea>
		        
		        <div class="notes">
		        <br /><strong>Previous Notes:</strong>
		        <br /><?php echo $transfer->notes; ?>
		        </div>
		   </div>
		   
		  
	   </div>
  	</div>
   
   <div class="row" id="productresults">
   		<div class="table-responsive">
				<div class="table">
				<?php if(count($products) > 0) { ?>
				        <h4>
				            Enter the stock amount to calculate the new stock in hand
				        </h4>
				        <table class="table table-striped">
				            <thead>
				                 <tr>
				                    <th width="15%">
				                        SKU
				                    </th>
				                    <th width="25%">
				                        SKU Name
				                    </th>
				               		<th width="15%">
				                        From Location SOH
				                    </th>
				                    <!-- 
				                    <th width="15%">
				                        To Location SOH
				                    </th>
				                     -->
				                    <th width="10%">
				                       Transfer Amount
				                    </th>
				                     <th width="10%">
				                        New From Location SOH
				                    </th>
				                    <!-- 
				                     <th width="10%">
				                        New To Location SOH
				                    </th>
				                     --> 
				                </tr>
				            </thead>
				            <tbody>
				        <?php 
				        		
				        		
				                $i = 0;
				                foreach($products as $product) {
									
									$amount = $product->item_quantity;
									
				                    $fromSOHData = $inventoryModelObj->get(array('sku_id' => $product->id , 'location_id' => $transfer->from_location_id));
				                    $toSOHData = $inventoryModelObj->get(array('sku_id' => $product->id , 'location_id' => $transfer->to_location_id));
				                    
				                  	 $fromSOHValue =  isset($fromSOHData[0])?$fromSOHData[0]->SOH:0;
				                	 $toSOHValue =  isset($toSOHData[0])?$toSOHData[0]->SOH:0;
				                    
				                	 $amountProperty = "";
				                    if ( $stage == 'In Transit') {
										$fromSOHValue = $fromSOHValue + $product->item_quantity;
									//	$toSOHValue = $toSOHValue - $product->item_quantity;
										$amountProperty = "readonly";
										
										$newFromSOHValue = $fromSOHValue - ($product->item_quantity);
										$newToSOHValue = $toSOHValue + ($product->item_quantity);
			                    } else {
				                    	$newFromSOHValue = $fromSOHValue - ($product->item_quantity);
										$newToSOHValue = $toSOHValue + ($product->item_quantity);
	
				                    }
				                    
				            ?>
				                <tr>
				                    <td>
				                        <?php echo $product->sku_value; ?>
				                    </td>
				                    <td>
				                       <?php echo $product->name; ?>
				                    </td>
				                   <td>
				                    	<?php echo $fromSOHValue; ?>
				                    	<input type="hidden" name="product[<?php echo $i; ?>]" id="product_<?php echo $i; ?>" value="<?php echo $fromSOHValue; ?>" />
				                   		<input type="hidden" name="to_product[<?php echo $i; ?>]" id="to_product_<?php echo $i; ?>" value=" <?php echo $toSOHValue?>" />    	 
				                   </td>
				                   <!-- 
				                   <td></td>  -->
				                
				                    <td>
				                    	
				                       <input type="text" name="amount[<?php echo $i; ?>]" id="amount_<?php echo $i; ?>" onblur="updateStock(<?php echo $i; ?>);" value="<?php echo $product->item_quantity; ?>" style="width:60px;" class="form-control" <?php echo $amountProperty; ?> />
				                    </td>
				                    <td>
				                    	<span id="new_from_<?php echo $i; ?>"><?php echo $newFromSOHValue; ?></span>
				                    	 <input type="hidden" name="new_from_product[<?php echo $i; ?>]" id="new_from_product_<?php echo $i; ?>" value="<?php echo $newFromSOHValue; ?>" readonly="readonly" />
				                    	 <input type="hidden" name="new_to_product[<?php echo $i; ?>]" id="new_to_product_<?php echo $i; ?>" value="<?php echo $newToSOHValue; ?>" readonly="readonly" />
				                    </td>
				                    <!-- 
				                    <td>
				                    	<span id="new_to_<?php echo $i; ?>"><?php echo $newToSOHValue; ?></span>
				                    	
				                    </td>  -->
				                    <input type="hidden" name="item_id[<?php echo $i; ?>]" id="item_<?php echo $i; ?>" value="<?php echo $product->id; ?>" />
				                   
				                    
				                </tr>
				        
				        <?php 
				                $i++;
				                } 
				            ?>
				            <input type="hidden" name="count" value="<?php echo $i; ?>" /> 
				            </tbody>
				        </table>
				         <div class="form-group">
				         <?php if ((!isset($stage)) || $stage == 'Draft') {?>
				            <input type="submit" class="btn btn-success" value="Submit" name="action" />&nbsp;<input type="submit" class="btn btn-primary" value="Save Draft" name="action" />&nbsp;<a href="<?php echo site_url('stocktransfer/listall/'); ?>" class="btn btn-warning">Cancel</a>
				         <?php } else if($stage == 'Ordered') { ?>
				         <input type="submit" class="btn btn-primary" value="Pick and Pack" name="action" />&nbsp;<a href="<?php echo site_url('stocktransfer/cancel/'.$transferId); ?>" class="btn btn-warning">Cancel</a>
				         <?php } else if($stage == 'Pick and Pack') { ?>
				         <input type="submit" class="btn btn-primary" value="Pick and Pack - Complete" name="action" />&nbsp;<a href="<?php echo site_url('stocktransfer/cancel/'.$transferId); ?>" class="btn btn-warning">Cancel</a>
				         <?php } else if($stage == 'Picked and Packed') { ?>
				         <input type="submit" class="btn btn-primary" value="Dispatch" name="action" />&nbsp;<a href="<?php echo site_url('stocktransfer/cancel/'.$transferId); ?>" class="btn btn-warning">Cancel</a>
				         <?php } else if($stage == 'In Transit') { ?>
				         <input type="submit" class="btn btn-primary" value="Receive" name="action" />&nbsp;
				         <?php } ?>
				           <?php if (isset($transferId)) {?>
				            <input type="hidden" name="transferId"  value="<?php echo $transferId; ?>" />
				           <?php } ?>
				         </div>
				
				<?php } else { ?>
				<div class="alert alert-warning">No Items Found For This Warehouse</div>
				<?php } ?>
				</div>
				</div>
   
   </div>
 </form>
</div>
</div>

<script>
var loadProducts = function () {

	if(validateForm()) {
		value = document.getElementById('from_location').value;
		tovalue = document.getElementById('to_location').value;
		frieght_location = document.getElementById('freight_location').value;
		
		document.getElementById('productresults').innerHTML = "<i class=\"fa fa-refresh fa-spin\"></i>&nbsp;&nbsp;Checking for existing values.";
		$.get( "<?php echo site_url("Stocktransfer/checkStatus/");?>/"+ value + "/" + tovalue + "/" + frieght_location)
		  .done(function( data ) {
			  returnValue = JSON.parse(data);
			  
			  if(returnValue.status == 'error') {
				  document.getElementById('productresults').innerHTML = "<i class=\"fa fa-refresh fa-spin\"></i>&nbsp;&nbsp;No data found. Loading forms...Please wait.";	
					  $.get( "<?php echo site_url("Stocktransfer/products/");?>/"+ value + "/" + tovalue )
			    	  .done(function( data ) {
			    	    	document.getElementById('productresults').innerHTML = data;
			    	  });
					  $('#currentstatus').val("");
						
				  } else {
					  document.getElementById('productresults').innerHTML = "<i class=\"fa fa-refresh fa-spin\"></i>&nbsp;&nbsp;Loading data...Please wait.";
					  
						$('#currentstatus').val(returnValue.stage);
						
					  
					  $.get( "<?php echo site_url("Stocktransfer/transferitems/");?>/"+ value + "/" + tovalue + "/" + returnValue.id )
			    	  .done(function( data ) {
			    	    	document.getElementById('productresults').innerHTML = data;
			    	  });
				} 
		    
		  });
	
	}
	
};

var updateStock = function(id) {
	valueFromOld = $('#product_'+id).val();
	valueToOld = $('#to_product_'+id).val();
	
	amount = $('#amount_'+id).val();
	newvalueFrom = parseInt(valueFromOld)-parseInt(amount);
	newvalueTo = parseInt(valueToOld)+parseInt(amount);
	
	if (amount != "" && amount >= 0) { 
		   $('#new_from_'+id).text(newvalueFrom);
		   $('#new_from_product_'+id).val(newvalueFrom);
		   $('#new_to_'+id).text(newvalueTo);
		   $('#new_to_product_'+id).val(newvalueTo);
		   
	}  else if(amount < 0) {
	    alert("Value cannot be negative");
	}
 };   


 var validateForm = function ()
 {
 	dateValue = document.getElementById('date').value;
 	from_location = document.getElementById('from_location').value;
 	to_location = document.getElementById('to_location').value;
 	freight_location = document.getElementById('freight_location').value;
 	
 	if (dateValue ==   "" || from_location == "" || to_location == "" || freight_location ==""  ) {
 	 	console.log(dateValue +   " -- " + from_location + "--" + to_location + "--" + freight_location  );
 		bootstrap_alert('danger', 'Missing mandatory fields.');
 		return false;
 	} else if (!isFutureDate('<?php echo date('Y-m-d'); ?>', dateValue)) {
 		bootstrap_alert('danger', 'Date should be today or future date.');
 		return false;
 	}
 	return true;	
 };
</script>