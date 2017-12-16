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
                    <th width="15%">
                        To Location SOH
                    </th>
                    <th width="10%">
                       Transfer Amount
                    </th>
                     <th width="10%">
                        New From Location SOH
                    </th>
                     <th width="10%">
                        New To Location SOH
                    </th>
                </tr>
            </thead>
            <tbody>
        <?php 
                $i = 0;
                foreach($products as $product) {

					$amount = $product['amount'];
					$old_from_value = $product['From_Stock'];
					$old_to_value = $product['To_Stock'];
					
					if($amount > 0) {
						$new_from_value = $old_from_value-$amount;
						$new_to_value = $old_to_value+$amount;
					} else {
						$new_from_value = "";
						$new_to_value = "";
					}
                    
            ?>
                <tr>
                    <td>
                        <?php echo $product['SKU']; ?>
                    </td>
                    <td>
                       <?php echo $product['SKU_Name']; ?>
                    </td>
                    <td class="center">
                        <?php echo $product['From_Stock']; ?>
                        
                        <input type="hidden" name="product[<?php echo $i; ?>]" id="product_<?php echo $i; ?>" value=" <?php echo $old_from_value; ?>" />
                    </td>
                    <td>
                        <?php echo $product['To_Stock']; ?>
                         <input type="hidden" name="to_product[<?php echo $i; ?>]" id="to_product_<?php echo $i; ?>" value=" <?php echo $old_to_value; ?>" />
                    </td>
                    <td>
                    	
                       <input type="text" name="amount[<?php echo $i; ?>]" id="amount_<?php echo $i; ?>" onblur="updateStock(<?php echo $i; ?>);" value="<?php echo $amount; ?>" style="width:60px;" class="form-control" />
                    </td>
                    <td>
                    	<span id="new_from_<?php echo $i; ?>" class="badge badge-warning"><?php echo $new_from_value; ?></span>
                        <input type="hidden" name="new_from_product[<?php echo $i; ?>]" id="new_from_product_<?php echo $i; ?>" value="<?php echo $new_from_value; ?>" readonly="readonly" />
                    </td>
                    <td>
                    	<span id="new_to_<?php echo $i; ?>" class="badge badge-warning"><?php echo $new_to_value; ?></span>
                       <input type="hidden" name="new_to_product[<?php echo $i; ?>]" id="new_to_product_<?php echo $i; ?>" value="<?php echo $new_to_value; ?>" readonly="readonly" />
                    </td>
                    <input type="hidden" name="item_id[<?php echo $i; ?>]" id="item_<?php echo $i; ?>" value="<?php echo $product['SKU_ID']; ?>" />
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
<div class="alert alert-warning">No Products Found For This Warehouse</div>
<?php } ?>
</div>
</div>

