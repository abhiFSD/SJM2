<?php if(count($items)): ?>
    <form method="post">
        <table class="table table-bordered table-striped table-condensed cf">
            <thead class="cf">
                <tr>
                    <th>SKU</th>
                    <th>SKU Name</th>
                    <?php foreach($warehouses as $id => $name): ?>
                        <th width="11%"><?php print $name; ?></th>
                    <?php endforeach; ?>
                    <th data-orderable="false"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $i => $sku): ?>
                    <tr>
                        <td data-title="SKU Name">
                            <span>
                                <?php echo $sku->sku_value; ?>
                            </span>
                        </td>
                        <td>
                            <?php echo $sku->name; ?>
                        </td>
                        <?php foreach($warehouses as $id => $name): ?>
                            <?php $inventory_item = POW\InventoryItem::warehouse_sku($id, $sku->id); ?>
                            <?php $soh = $inventory_item->SOH ? $inventory_item->SOH : 0; ?>
                            <td>
                                <?php if ($inventory_item->SOH): ?>
                                    <input type="checkbox"
                                        class=""
                                        name="item[]" <?php echo $inventory_item->id ? 'checked="checked"' : ''; ?>
                                        value="<?php echo $sku->id; ?>"
                                        onclick="updateAssigment(this, <?php echo $id; ?>);" disabled="true" />&nbsp;&nbsp;
                                    <span class="label label-default"><?php echo $soh; ?></span>
                                <?php else: ?>
                                    <input type="checkbox"
                                        class="<?php echo $id; ?>_check <?php echo $i; ?>_rowcheck chk_item"
                                        name="item[]" <?php echo $inventory_item->id ? 'checked="checked"' : ''; ?>
                                        value="<?php echo $sku->id; ?>"
                                        onclick="updateAssigment(this, <?php echo $id; ?>);"  />&nbsp;&nbsp;
                                    <span class="label label-default"><?php echo $soh; ?></span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                        <td>
                            <input type="checkbox" onchange="toggle('<?php echo $i;?>_rowcheck',this)" />
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="form-group">
            <a href="<?php echo site_url('products/all'); ?>" class="btn btn-primary">Cancel</a>
        </div>
    </form>
<?php else: ?>
    <p>No data available.</p>
<?php endif; ?>
