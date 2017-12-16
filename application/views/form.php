

<div class="row">
<div class="col-md-12">
<form action="" method="post">
   <div class="form-group">
        <label>Warehouse:</label>
        <select id="warehouse" name="warehouse" onchange="changeWarehouse()" class="form-control">
            <option value="">Select Warehouse</option>
            <?php foreach($warehouses as $warehouse ) { ?>
                <option value="<?php echo $warehouse['Warehouse_ID'] ?>"><?php echo $warehouse['Warehouse_Name'] ?></option>
            <?php } ?>
            </select>
        
   </div>
   <div id="productresults">
   </div>
 </form>
</div>
</div>

<script>
var changeWarehouse = function () {
	value = document.getElementById('warehouse').value;
	document.getElementById('productresults').innerHTML = "Loading...Please wait.";
    if (value!="") {
    	$.get( "<?php echo site_url("stocktake/products/");?>/"+ value)
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
	
	$('#stock_count_'+id).val(value-amount);
 };
</script>