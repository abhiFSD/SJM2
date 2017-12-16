<div class="table-responsive">
    <div class="table">
        <?php if (count($products) > 0):?>
                <table class="table table-bordered table-striped table-condensed" id='product-table'>
                    <thead>
                        <tr>
                            <th>
                                POS
                            </th>
                            <th>
                                SKU
                            </th>
                            <th width="60%">
                                SKU Name
                            </th>
                            <th align="center" width="5%">
                                <span class="mobile-only">
                                    Previous
                                </span>
                                <span class="desktop-only">
                                    Previous Stock on hand
                                </span>
                            </th>
                            <th width="15%">
                                <span class="mobile-only">
                                    New
                                </span>
                                <span class="desktop-only">
                                   New stock on hand<br/>
                                   (leave blank if no change)
                               </span>
                            </th>
                            <th align="center" width="15%">
                                <span class="mobile-only">
                                    Change
                                </span>
                                <span class="desktop-only">
                                    Adjustment
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $i = 0;
                        foreach ($products as $product):
                    ?>
                            <tr>
                                <td data-title="POS">
                                    <input type="text" value="<?php echo $product['POSITION']; ?>" class="form-control w-70-px" name="position[<?php echo $i; ?>]" id="pos_<?php echo $i; ?>"/>
                                    <input type="hidden" name="position_checker[<?php echo $i; ?>]" id="pos_chk<?php echo $i; ?>" value="<?php echo $product['POSITION']; ?>"/>
                                </td>
                                <td data-title="SKU">
                                    <?php echo $product['SKU']; ?>
                                </td>
                                <td data-title="SKU Name">
                                    <?php echo $product['SKU_Name']; ?>
                                </td>
                                <td align="center" data-title="Previous Stock on hand">
                                    <?php echo $product['Previous_Stock']; ?>
                                    <input type="hidden" name="product[<?php echo $i; ?>]" id="product_<?php echo $i; ?>" value=" <?php echo $product['Previous_Stock']; ?>"/>
                                </td>
                                <td data-title="New stock on hand">
                                    <?php
                                        $amount_value = "";
                                        if (isset($formdata['amount'][$i])):
                                            $amount_value = $formdata['amount'][$i];
                                        endif;
                                    ?>
                                    <input type="number" step="1" value="<?php echo $amount_value; ?>" class="form-control stockTakeNewAmount w-100-px" name="amount[<?php echo $i; ?>]" id="amount_<?php echo $i; ?>" onchange="stockTakeNewUpdateStock(<?php echo $i; ?>);"/>
                                </td>
                                <td align="center" data-title="Adjustment">
                                    <?php
                                        $stock_value = "";
                                        if (isset($formdata['stock_count'][$i])):
                                             $stock_value = $formdata['stock_count'][$i];
                                        endif;
                                    ?>
                                    <span id="stock_display_<?php echo $i; ?>"
                                          class="badge badge-warning"><?php echo $stock_value; ?>
                                    </span>&nbsp;
                                    <input type="hidden" value="<?php echo $stock_value; ?>" name="stock_count[<?php echo $i; ?>]" id="stock_count_<?php echo $i; ?>" readonly/>
                                </td>
                                <input type="hidden" name="item_id[<?php echo $i; ?>]" id="item_<?php echo $i; ?>" value="<?php echo $product['SKU_ID']; ?>"/>
                            </tr>
                    <?php
                            $i ++;
                        endforeach;
                    ?>
                    </tbody>
                </table>
                <input type="hidden" name="count" value="<?php echo $i; ?>"/>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Submit" name="save"/>&nbsp;
                    <ahref="<?php echo site_url('stocktakenew/listall'); ?>" class="btn ">Cancel</a>
                </div>
        <?php else: ?>
                <div class="alert">No Items Found For This Warehouse</div>
        <?php endif; ?>
    </div>
</div>