<div>
    <table class="table table-bordered actions hide-sorting dataTable">
        <thead>
            <tr>
                <th>W/H Pos</th>
                <th>SKU</th>
                <th>Picked</th>
                <th width="1%">Transfer Stock</th>
            </tr>
        </thead>
        <tbody>
                <?php foreach ($transfer_items as $item): ?>
                    <?php if ($item->offering_attribute_id != 1) continue; ?>
                    <tr id="replenish-<?php print $item->position; ?>" data-position="<?php print $item->warehouse_position; ?>" class="replenish">
                        <td class="emphasize-small" scope="row"><?php echo $item->warehouse_position; ?></td>
                        <td class="emphasize-small" scope="row"><div class="fixwidth"><?php echo $item->sku_name; ?></div></td>
                        <td class="emphasize-small text-right"><?php echo $item->picked_quantity; ?></td>
                        <td>
                            <?php
                                $v = "display: none;";
                                $v1 = "display: block;";
                                if ($item->in_kiosk =="Yes")
                                {
                                    $v = "display: inline-table;";
                                    $v1 = "display: none;";
                                }
                            ?>
                            <div class="fixwidth">
                                <div class="input-group quickselect offering-validation" style="<?php echo $v1 ?>">
                                    <button 
                                        title="Click here to pick all quantity" 
                                        type="button" 
                                        data-value="<?php echo $item->picked_quantity?>"
                                        data-pick="0"
                                        class="btn btn-default btn-number quickfill-stock">
                                        <span class="glyphicon glyphicon-ok"></span>
                                    </button>
                                </div>
                                <div class="input-group controls control-box soh-boxes" data-val="<?php echo $item->in_kiosk?>" style="<?php echo $v; ?>">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-number" data-type="minus" data-field="num"><span class="glyphicon glyphicon-minus"></span></button>
                                    </span>
                                    <input type="hidden" name="offering[transfer_item_id][<?php print $item->position; ?>]" value="<?php print $item->id; ?>">
                                    <input type="hidden" name="transfer_stock[sku_id][<?php print $item->position; ?>]" value="<?php print $item->value; ?>">
                                    <input type="text" name="transfer_stock[amount][<?php print $item->position; ?>]" min="0" class="form-control input-number controls soh-item" value="<?php echo $item->picked_quantity; ?>">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="num"><span class="glyphicon glyphicon-plus"></span></button>
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
        </tbody>
    </table>
</div>