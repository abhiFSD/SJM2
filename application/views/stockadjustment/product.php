<div class="table">
<?php if(count($products) > 0) { ?>
        <p>
            Enter the stock amount to calculate the new stock in hand
        </p>
        <table class="table table-striped">
            <thead>
                 <tr>
                    <th>
                        SKU
                    </th>
                    <th>
                        SKU Name
                    </th>
                    <th>
                        Previous stock on hand
                    </th>
                    <th>
                       Amount
                    </th>
                    <th>
                        New stock on hand
                    </th>
                    
                </tr>
            </thead>
            <tbody>
        <?php 
                $i = 0;
                foreach($products as $product) {
                    
            ?>
                <tr>
                    <td>
                        <?php echo $product['SKU']; ?>
                    </td>
                    <td>
                       <?php echo $product['SKU_Name']; ?>
                    </td>
                    <td>
                        <?php echo $product['Previous_Stock']; ?>
                        <input type="hidden" name="product[<?php echo $i; ?>]" id="product_<?php echo $i; ?>" value=" <?php echo $product['Previous_Stock']; ?>" />
                    </td>
                    <td>
                       <input type="text" name="amount[<?php echo $i; ?>]" id="amount_<?php echo $i; ?>" onblur="updateStock(<?php echo $i; ?>);" />
                    </td>
                    <td>
                        <input type="text" name="stock_count[<?php echo $i; ?>]" id="stock_count_<?php echo $i; ?>" readonly />
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
            <input type="submit" class="btn btn-primary" value="Save Data" name="save" />
         </div>

<?php } else { ?>
<div class="alert">No Items Found For This Warehouse</div>
<?php } ?>
</div>
<script>

          

</script>
