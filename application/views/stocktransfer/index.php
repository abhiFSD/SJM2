<div class="row">
<div class="col-md-12">
<form action="" method="post">
	<h2>Stock Transfer Form</h2>
	<div class="row alert-custom showboka filter_section" id="filter_section">
		<div class="col-md-4">
		
	    <div class="form-group">
	        <label>From: *</label>
	        <select id="from_location" name="from_location" onchange="" class="form-control">
	        <option value="">Select Location</option>
	                <?php foreach($locations as $location ) { ?>
	                    <option value="<?php echo $location['id'] ?>"><?php echo $location['name'] ?></option>
	                <?php } ?>
	                </select>
	        
	   </div>
	   <div class="form-group">
	        <label>To: *</label>
	         <select id="to_location" name="to_location" onchange="" class="form-control">
	                    <option value="">Select Location</option>
	                <?php foreach($locations as $location ) { ?>
	                    <option value="<?php echo $location['id'] ?>"><?php echo $location['name'] ?></option>
	                <?php } ?>
	       </select>
	   </div>

	   		   <div class="form-group">
		        <label>Notes:</label>
		        <span>
		        <textarea name="notes" class="form-control" ></textarea>
		        </span>
		   </div>
	 <!--   <div class="form-group">
	        <label>Freight Location: *</label>
	         <select id="freight_location" name="freight_location" onchange="" class="form-control">
	                    <option value="">Select Location</option>
	                <?php foreach($freightLocations as $location ) { ?>
	                    <option value="<?php echo $location['id'] ?>"><?php echo $location['name'] ?></option>
	                <?php } ?>
	       </select>
	   </div>
	     -->
	   </div>
	   <div class="col-md-4">
	   
		   <div class="form-group">
		        <label>Current Status:</label>
		        
		        <input type="text" name="currentstatus"   value="" class="form-control" readonly />
		   </div>
		   <div class="form-group">
		        <label>Current Location:</label>
		        <input type="text" name="currentlocation" value="" class="form-control" readonly />
		   </div>

		  
		   <div class="form-group">
			   <label></label>
		       <input type="button" onclick="loadProducts()" style="margin-top: 20px;" class="btn btn-primary"  value="Load Available Items"/>
		   </div>
	   </div>
  	</div>
   
   <div class="row" id="productresults">
   </div>
 </form>
</div>
</div>

<script>


var loadProducts = function () {

	if(validateForm()) {
		value = document.getElementById('from_location').value;
		tovalue = document.getElementById('to_location').value;
		//frieght_location = document.getElementById('freight_location').value;
		
		document.getElementById('productresults').innerHTML = "<i class=\"fa fa-refresh fa-spin\"></i>&nbsp;&nbsp;Checking for existing values.";
		$.get( "<?php echo site_url("Stocktransfer/checkStatus/");?>/"+ value + "/" + tovalue )
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
	from_location = document.getElementById('from_location').value;
	to_location = document.getElementById('to_location').value;

	if (from_location == "" || to_location == "" ) {
		bootstrap_alert('danger', 'Missing mandatory fields.');
		return false;
	} 
	return true;
};

</script>