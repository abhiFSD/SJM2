<?php
$parts = array();
foreach ($items as $key => $item)
{
    if (0 === strcasecmp($item->product_type, 'product')) continue;

    $parts[] = $item;
}
?>

<h4>Planagram Changes</h4>
<?php if (count($planagram_changes)): ?>
    <?php $planagram_changes = array_pop($planagram_changes); ?>
    <?php $contents = $this->load->view('picks/sections/atkiosk_planagram', ['planagram_changes' => $planagram_changes, 'parts' => $parts], true); ?>
    <?php if (!empty(trim($contents))): ?>
        <table class="table table-bordered actions hide-sorting">
            <thead>
                <tr>
                    <th>POS</th>
                    <th>Attribute</th>
                    <th>Item(s) to Remove/Change</th>
                    <th>Item(s) to Install/Change</th>
                    <th <?php if (!empty($GLOBALS['current_stock'])) print 'width="1%"'; ?>></th>
                </tr>
            </thead>
            <tbody>
                <?php print $contents; ?>
            </tbody>
        </table>
    <?php else: ?>
        <?php $contents = ''; ?>
    <?php endif; ?>
<?php endif; ?>
<?php if (empty($planagram_changes) || empty($contents)): ?>
    <p><strong>None.</strong></p>
<?php endif; ?>

<div class="single-kiosk-table">
    <h4>Allot Products</h4>
    <table id="table-stock-kiosk" class="table table-bordered actions dataTable">
        <thead>
            <tr>
                <th>Pos</th>
                <th>SKU</th>
                <th>System SOH</th>
                <th>Picked</th>
                <th>Swap</th>
                <th class="w-70-px">New SOH</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($items) > count($parts)): ?>
                <?php foreach ($items as $item): ?>
                    <?php 
                    if (0 !== strcasecmp($item->product_type, 'product')) continue;

                    $soh = $item->soh;
                    
                    if (isset($item->isnew))
                    {
                        if($item->isnew == "Queued")
                        {
                            $soh = 0;
                        }

                        if($item->picked_quantity==0)
                        {
                            $item->picked_quantity = '';
                        }
                    }
                    ?>
                    <tr id="replenish-<?php print $item->position; ?>" data-position="<?php print $item->position; ?>" class="replenish">
                        <td class="emphasize-small" scope="row"><?php echo $item->position; ?></td>
                        <td class="emphasize-small" scope="row"><div class="fixwidth"><?php echo $item->name; ?> </div></td>
                        <td class="emphasize-small text-right"><?php echo $soh; ?></td>
                        <td class="emphasize-small text-right"><?php echo $item->picked_quantity; ?></td>
                        <td class="emphasize-small text-right">
                            <?php if (!empty($GLOBALS['current_stock'][$item->skuid])): ?>
                                <?php $swap_value = 0; ?>
                                <?php 
                                    if ($transfer->fill_level == 'par')
                                    {
                                        $attributes = POW\OfferingAttributeAllocation::with_offering_attribute_id($transfer->location_to_id, $item->position, 6);
                                    }
                                    else
                                    {
                                        $attributes = POW\OfferingAttributeAllocation::with_offering_attribute_id($transfer->location_to_id, $item->position, 5);
                                    }

                                    if (count($attributes))
                                    {
                                        $active = $attributes[0];
                                        $space = $active->value - $item->soh - $item->picked_quantity;

                                        if ($space < 0)
                                        {
                                            $swap_value = '';
                                        }
                                        elseif ($space <= $GLOBALS['current_stock'][$item->skuid])
                                        {
                                            $swap_value = $space;
                                            $GLOBALS['current_stock'][$item->skuid] -= $space;
                                        }
                                        else
                                        {
                                            $swap_value = $GLOBALS['current_stock'][$item->skuid];
                                            $GLOBALS['current_stock'][$item->skuid] = 0;
                                        }
                                    }
                                ?>
                                <?php print $swap_value; ?>
                                <input type="hidden" name="swap[<?php print $item->skuid; ?>][]" class="swap-stock" value="<?php print $swap_value; ?>">
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                                $newsoh = 0;
                                $v = "display: none;";
                                $v1 = "display: block;";
                                if ($item->in_kiosk =="Yes")
                                {
                                    $v = "display: inline-table;";
                                    $v1 = "display: none;";
                                    $newsoh = $item->picked_quantity + $item->soh;
                                }
                            ?>
                            <div class="fixwidth">
                                <div class="input-group quickselect offering-validation" style="<?php echo $v1 ?>">
                                    <button 
                                        data-html="false"
                                        data-toggle="popover" 
                                        title="Hint" 
                                        data-content="Click here to pick all quantity" 
                                        type="button" 
                                        data-pick="<?php echo $item->picked_quantity?>"  
                                        data-value="<?php echo $item->soh?>" 
                                        class="btn btn-default btn-number quickfill-stock">
                                        <span class="glyphicon glyphicon-ok"></span>
                                    </button>
                                </div>
                                <div class="input-group controls control-box soh-boxes" data-val="<?php echo $item->in_kiosk?>" style="<?php echo   $v  ?>">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-number" data-type="minus" data-field="num"><span class="glyphicon glyphicon-minus"></span></button>
                                    </span>
                                    <?php if (!empty($item->transfer_item_id)): ?>
                                        <input type="hidden" name="transfer_item_id[<?php print $item->position; ?>]" value="<?php print $item->transfer_item_id; ?>">
                                        <input type="hidden" name="offering[transfer_item_id][<?php print $item->position; ?>]" value="<?php print $item->transfer_item_id; ?>">
                                    <?php endif; ?>
                                    <input type="hidden" name="offering[sku][<?php echo $item->position; ?>]"   value="<?php echo $item->skuid?>">
                                    <input type="hidden" name="offering[picked][<?php echo $item->position; ?>]"   value="<?php echo $item->picked_quantity?>">
                                    <input type="hidden" name="offering[soh][<?php echo $item->position; ?>]"   value="<?php echo $item->soh?>">
                                    <input type="text" name="offering[newsoh][<?php echo $item->position; ?>]" min="0" max="" class="form-control input-number controls soh-item" value="<?php echo $newsoh ?>">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="num"><span class="glyphicon glyphicon-plus"></span></button>
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>