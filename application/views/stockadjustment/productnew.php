<div class="table-responsive12 m-tb-20-px">
    <div id="bulk-buttons" style="display: none">
    </div>
    <div class="table">
    <?php if(count($products) > 0) : ?>
             <table class="table table-bordered table-striped table-condensed cf"  id="stock-adjustment-table">
                <thead class="cf">
                     <tr>
                         <th>
                             POS
                         </th>
                        <th class="desktop-only">
                            SKU
                        </th>
                        <th width="60%">
                            SKU Name
                        </th>
                        <th width="15%">
                            <span class="mobile-only">
                                Change
                            </span>
                            <span class="desktop-only">
                           Adjustment<br />
                           (leave blank if no change)
                           </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $i = 0;
                        foreach($products as $product) :
                    ?>
                            <tr>
                                <td data-title="POS">
                                    <?php echo $product['POSITION']; ?>
                                </td>
                                <td class="desktop-only">
                                    <?php echo $product['SKU']; ?>
                                </td>
                                <td data-title="SKU Name">
                                   <?php echo $product['SKU_Name']; ?>
                                    <span class="mobile-only"><br/>
                                     <?php echo $product['SKU']; ?>
                                     </span>
                                </td>
                                  <input type="hidden" name="product[<?php echo $i; ?>]" id="product_<?php echo $i; ?>" value=" <?php echo $product['Previous_Stock']; ?>" />
                                <td data-title="Adjustment">
                                    <?php
                                        $amount_value = "";

                                        if (isset($formdata['amount'][$i])) {

                                            $amount_value = $formdata['amount'][$i];
                                        }
                                    ?>
                                   <input type="number" name="amount[<?php echo $i; ?>]" value="<?php echo $amount_value; ?>" id="amount_<?php echo $i; ?>" onblur="indexNewupdateStock(<?php echo $i; ?>);" style="width:80px;" class="form-control" />
                                </td>
                                 <?php
                                        $stock_value = "";
                                        if (isset($formdata['stock_count'][$i])) {
                                            $stock_value = $formdata['stock_count'][$i];
                                        }
                                    ?>
                                <input type="hidden" name="item_id[<?php echo $i; ?>]" id="item_<?php echo $i; ?>" value="<?php echo $product['SKU_ID']; ?>" />
                                <input type="hidden" name="stock_count[<?php echo $i; ?>]" value="<?php echo $stock_value; ?>" id="stock_count_<?php echo $i; ?>" readonly />
                            </tr>
                    <?php
                            $i++;
                        endforeach;
                    ?>
                    <input type="hidden" name="count" value="<?php echo $i; ?>" />
                </tbody>
             </table>
             <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit" name="save" />&nbsp;<a href="<?php echo site_url('stocktakenew/listall'); ?>" class="btn ">Cancel</a>
             </div>

    <?php else : ?>
        <div class="alert">No Items Found For This Warehouse</div>
    <?php endif; ?>
    </div>
</div>