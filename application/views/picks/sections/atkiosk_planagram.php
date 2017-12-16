<?php
$attribute_labels = POW\OfferingAttribute::list_key_val('id', 'name');
$attribute_id_map = get_attribute_id_map();

foreach ($planagram_changes as $position => $change):

    $attribute_ids_top = [];
    $attribute_ids_bottom = [];

    foreach ($change['Queued']['queue_status'] as $attribute_id)
    {
        // price must be displayed last
        if (in_array($attribute_id, [2, 3]))
        {
            $attribute_ids_bottom[] = +$attribute_id;
        }
        else
        {
            $attribute_ids_top[] = +$attribute_id;
        }
    }

    sort($attribute_ids_top);
    sort($attribute_ids_bottom);
    $attribute_ids = array_merge($attribute_ids_top, $attribute_ids_bottom);

    foreach ($attribute_ids as $attribute_id):

        // not part of atkiosk
        if (in_array($attribute_id, [4, 7, 8])) continue;

        // must have SKU in transfer
        // capacity, par and price require an SKU change to be displayed in planagram section
        if (in_array($attribute_id, [1, 2, 3, 5, 6]))
        {
            // SKU is offering_attribute_id 1
            $match = match_attribute($items, $position, 1, $change['Queued']['skuid']);

            if (!$match) continue;

            // disabled checking for picked_quantity
            // full position swaps will have 0 pick and picked quantity 
            // but need individual transfer_items to identify sku change
        }

        // must have a pick
        if (in_array($attribute_id, [9, 10, 11, 12]))
        {
            $match = match_attribute($parts, $position, $attribute_id, $change['Queued'][$attribute_id]['skuid']);

            if (!$match) continue;

            if ($match && $match->picked_quantity == 0) continue;
        }

        // label must have a pick
        if (13 == $attribute_id)
        {
            $match = match_attribute($parts, $position, $attribute_id, '');

            if (!$match) continue;
        }

        ?>
        <tr class="planagram" data-position="<?php print $position; ?>" data-attribute-id="<?php print $attribute_id; ?>">
            <td><?php print $position; ?></td>
            <td><?php print $attribute_labels[$attribute_id]; ?></td>

            <?php if (1 == $attribute_id): ?>
                <td><?php print $change['Active']['product']; ?></td>
                <td></td>
            <?php elseif (in_array($attribute_id, [2, 3, 5, 6])): ?>
                <td><?php print (in_array($attribute_id, [2, 3]) ? '$' : '').$change['Active'][$attribute_id_map[$attribute_id]]; ?></td>
                <td><?php print (in_array($attribute_id, [2, 3]) ? '$' : '').$change['Queued'][$attribute_id_map[$attribute_id]]; ?></td>
            <?php elseif (in_array($attribute_id, [9, 10, 11, 12])): ?>
                <td><?php if (!empty($change['Active'][$attribute_id_map[$attribute_id]])) print $change['Active'][$attribute_id_map[$attribute_id]]; ?></td>
                <td><?php if (!empty($change['Queued'][$attribute_id_map[$attribute_id]])) print $change['Queued'][$attribute_id_map[$attribute_id]]; ?></td>
            <?php else: // label ?>
                <td><?php print $position.' '.$change['Active']['item'].' $'.$change['Active']['price3']; ?></td>
                <td><?php print $position.' '.(in_array(1, $change['Queued']['queue_status']) ? $change['Queued']['item'] : $change['Active']['item']).' $'
                    .(in_array(2, $change['Queued']['queue_status']) ? $change['Queued']['price'] : $change['Active']['price3']); ?>
                </td>
            <?php endif; ?>

            <?php if (1 != $attribute_id): ?>
                <td>
                    <div class="fixwidth">
                        <div class="input-group quickselect">
                            <input type="hidden" name="planagram_change[<?php print $position; ?>][<?php print $attribute_id; ?>]" value="1">
                            <span style="display: none;" class="label label-success">Done</span>
                            <button   data-html="false" type="button" class="btn btn-default btn-number quickfill-commit-3"><span class="glyphicon glyphicon-ok"></span></button>
                            <span style="display: none;" class="label label-default">Not Done</span>
                            <button   data-html="false"  type="button" class="btn btn-default btn-number quickfill-remove"><span class="glyphicon glyphicon glyphicon-remove"></span></button>
                            <button  type="button"  style="display: none;" class="btn btn-default btn-number quickfill-reset">RESET</button>
                        </div>
                    </div>
                </td>
            <?php else: ?>
                <td>
                    <input type="hidden" name="planagram_change[<?php print $position; ?>][<?php print $attribute_id; ?>]" value="1">
                    <input type="hidden" name="return_to_warehouse[sku][<?php print $position; ?>]" value="<?php print $change['Active']['skuid']; ?>">
                    <div class="input-group controls">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default btn-number" data-type="minus" data-field="num"><span class="glyphicon glyphicon-minus"></span></button>
                        </span>
                        <input type="text" name="return_to_warehouse[soh][<?php print $position; ?>][<?php print $attribute_id; ?>]" min="0" value="<?php print $change['Active']['on_hand']; ?>" class="controls input-number form-control" data-skuid="<?php print $change['Active']['skuid']; ?>">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="num"><span class="glyphicon glyphicon-plus"></span></button>
                        </span>
                    </div>
                    <?php
                    if (!empty($change['Active']['on_hand']))
                    {
                        if (!empty($GLOBALS['current_stock'][+$change['Active']['skuid']]))
                        {
                            $GLOBALS['current_stock'][+$change['Active']['skuid']] += $change['Active']['on_hand'];
                        }
                        else
                        {
                            $GLOBALS['current_stock'][+$change['Active']['skuid']] = $change['Active']['on_hand'];
                        }
                    }
                    ?>
                </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
<?php endforeach; ?>
