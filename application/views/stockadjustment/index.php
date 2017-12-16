<div class="row">
<div class="col-md-12">
<form action="" method="post">
    <div class="form-group">
        <label>Location:</label>
        <select id="location" name="location" onchange="changeLocation()" class="form-control">
        <option value="">Select Location</option>
                <?php foreach($locations as $location ) { ?>
                    <option value="<?php echo $location['Inv_Locn_ID'] ?>"><?php echo $location['Inv_Locn_Name'] ?></option>
                <?php } ?>
                </select>
        
   </div>
   <div class="form-group">
        <label>Adjustment Type:</label>
        <select id="adjustment" name="adjustment" onchange="changeAdjustment()" class="form-control">
                    <option value="">Select Adjustment</option>
               
                    <option value="1">Stock Order</option>
                    <option value="2">Inbound Interstate Transfer</option>
                    <option value="3">From Kiosk</option>
                    <option value="4">Pick List</option>
                    <option value="5">Outbound Interstate Transfer</option>
                    <option value="6">Pick Adjustment</option>
                    <option value="7">Other</option>
                </select>
        
   </div>
   <div class="form-group adjustment_fields field_1">
        <label>Order Number:</label>
        <input type="text" name="ordernumber" value="" class="form-control">
   </div>
    <div class="form-group adjustment_fields field_2">
        <label>Received From:</label>
        <select id="received_location" name="received_from" class="form-control">
                    <option value="">Select Location</option>
                <?php foreach($locations as $location ) { ?>
                    <option value="<?php echo $location['Inv_Locn_ID'] ?>"><?php echo $location['Inv_Locn_Name'] ?></option>
                <?php } ?>
       </select>
   </div>
   <div class="form-group adjustment_fields field_3">
        <label>From Kiosk:</label>
        <input type="text" name="from_machine" value="" class="form-control">
   </div>
    <div class="form-group adjustment_fields field_4" >
        <label>Pick List:</label>
        <input type="date" name="pick_list" value="" class="form-control">
   </div>
   <div class="form-group adjustment_fields field_5">
        <label>Sent To:</label>
        <select id="sent_location" name="sent_to" class="form-control">
                    <option value="">Select Location</option>
                <?php foreach($locations as $location ) { ?>
                    <option value="<?php echo $location['Inv_Locn_ID'] ?>"><?php echo $location['Inv_Locn_Name'] ?></option>
                <?php } ?>
       </select>
   </div>
   <div class="form-group adjustment_fields field_6">
        <label>Pick Adjustment:</label>
        <input type="date" name="pick_adjustment" value="" class="form-control">
   </div>
   
    <div class="form-group adjustment_fields field_7">
        <label>Other:</label>
        <input type="text" name="other" value="" class="form-control">
   </div>
   <div id="productresults">
   </div>
 </form>
</div>
</div>

<script>
var changeLocation = function () {
	value = document.getElementById('location').value;
	document.getElementById('productresults').innerHTML = "Loading...Please wait.";
    if (value!="") {
    	$.get( "<?php echo site_url("Stockadjustment/products/");?>/"+ value)
    	  .done(function( data ) {
    	    document.getElementById('productresults').innerHTML = data;
    	  });
    } else {
    	document.getElementById('productresults').innerHTML = "";
        }
	
};

var updateStock = function(id) {
	value = $('#product_'+id).val();
	amount = $('#amount_'+id).val();
	newvalue = parseInt(value)+parseInt(amount);
	if (newvalue >= 0) { 
		   $('#stock_count_'+id).val(newvalue);
	} else {
	    alert("Value cannot be negative");
	}
 };

 var changeAdjustment = function ()
 {
	 value = document.getElementById('adjustment').value;
	 $('.adjustment_fields').hide();
	 $('.field_'+value).show();
	 
	    	   
 };
</script>