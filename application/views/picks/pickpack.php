<div class="row">
    <div class="col-md-12">
        <h3 class="page-header">Pick & Pack</h3>
    </div>
    <div class="col-md-12">
        <div id="kiosk_selection" class="single-kiosk-table">
            <h4 class="heading-label">
                Destination
                <?php if ($transfer->location_to_type == 'kiosk'): ?>
                    <?php print kiosk_page_link($transfer->location_to, 'long'); ?>
                <?php else: ?>
                    <?php print inventory_location_page_link($transfer->location_to); ?>
                <?php endif; ?>
            </h4>
            <form name="pick-pack-form" id="pick-pack-form">
                <input type="hidden" id="pick-type" name="type"  value="">
                <table id="table-product-1" class="table table-bordered actions dataTable">
                    <thead>
                        <tr>
                            <th class="w-100-px">W/HOUSE POS</th>
                            <th class="w-90-px">KIOSK POS</th>
                            <th>Type</th>
                            <th>SKU</th>
                            <th>Pick</th>
                            <th width="1%">Picked</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($items): ?>
                            <?php foreach ($items as $key => $item): ?>
                                <?php
                                if (!$item->pick_quantity) continue;

                                $pickable_quantity = $item->pick_quantity - $item->picked_quantity;
                                $pickable_quantity = $pickable_quantity > 0 ? $pickable_quantity : 0;
                                $picked = $item->picked_quantity ? $item->picked_quantity : $item->pick_quantity;
                                ?>
                                <tr>
                                    <td class="emphasize-small" scope="row"><?php echo $item->warehouse_position; ?></td>
                                    <td class="emphasize-small" scope="row"><?php echo $item->position; ?></td>
                                    <td class="emphasize-small" scope="row">
                                        <?php if ($item->product_type): ?>
                                            <?php echo $item->product_type; ?>
                                        <?php elseif ($item->offering_attribute_id == 13): ?>
                                            <?php echo 'Label'; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="emphasize-small" scope="row"><?php echo $item->sku_name; ?></td>
                                    <td class="emphasize-small text-right"><?php echo $item->pick_quantity?></td>
                                    <td class="text-center">
                                        <?php if (!$item->picked_quantity): ?>
                                            <div class="input-group quickselect text-center">
                                                <button
                                                data-html="false"
                                                data-toggle="popover"
                                                title="Hint"
                                                data-content="Click here to pick all quantity"
                                                type="button"
                                                data-value="<?php echo $picked ?>" class="btn btn-default btn-number quickfill">
                                                <span class="glyphicon glyphicon-ok"></span>
                                                </button>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!$item->picked_quantity): ?>
                                            <div class="input-group controls control-box soh-boxes text-center" style="display: none;">
                                        <?php else:  ?>
                                            <div class="input-group controls control-box text-center">
                                        <?php endif; ?>
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default btn-number" data-type="minus" data-field="num"><span class="glyphicon glyphicon-minus"></span></button>
                                            </span>
                                            <input type="hidden" name="inventory_location_id[]"   value="<?php echo $transfer->location_from_id; ?>">
                                            <input type="hidden" name="transfer_item_id[]"   value="<?php echo $item->id; ?>">
                                            <input type="hidden" name="transfer_current_quatity[]"   value="<?php echo $pickable_quantity; ?>">
                                            <input type="hidden" name="initial_pick_quatity[]"   value="<?php echo  $item->pick_quantity; ?>">
                                            <input type="hidden" name="current_picked_quatity[]"   value="<?php echo  $item->picked_quantity; ?>">
                                            <?php if ($item->picked_quantity >= 0): ?>
                                            <input type="text" name="transfer_item_quantity[]" min="0" max="1000" class="form-control input-number controls collapsed-value"  value="<?php echo $item->picked_quantity; ?>">
                                            <?php else: ?>
                                            <input type="text" name="transfer_item_quantity[]" min="0" max="1000" class="form-control input-number controls collapsed-value" data-value="<?php echo $item->picked_quantity; ?>" value="">
                                            <?php endif; ?>
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="num"><span class="glyphicon glyphicon-plus"></span></button>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if (isset($selections) && sizeof($selections)>0 ): ?>
                    <h4 style="margin-top: 40px;">Queued Kiosk Attribute Changes </h4>
                    <hr/>
                    <table id="table-product-s" class="table table-bordered actions">
                        <thead>
                            <tr>
                                <th>Attribute</th>
                                <th>Current Value</th>
                                <th>Queued Value</th>
                                <th>Picked</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0; ?>
                            <?php if (isset($selections) && $selections): ?>
                            <?php foreach($selections as $selection): ?>
                            <tr class="value_rows">
                                <td><input type ="text" class="no-show-input" value="<?php echo $selection->name; ?>" readonly/></td>
                                <td> <div id="values_queued"> <?php echo $selection->current_value; ?> </div> </td>
                                <td> <?php echo $selection->value; ?> </td>
                                <td>
                                    <div class="input-group quickselect">
                                        <span style="display: none;" class="label label-success picked-label">Picked</span>
                                        <button   data-html="false"  data-toggle="popover"  data-content="Click here to pick all quantity" type="button" id="chkbox<?php echo $i.'-'. $selection->id; ?>" data-currentid="<?php echo $selection->current_id; ?> " data-id ="<?php echo $selection->id ?>" class="btn btn-default btn-number quickfill-commit"  ><span class="glyphicon glyphicon-ok"></span></button>
                                        <span style="display: none;" class="label label-default">Not Picked</span>
                                        <button   data-html="false"  data-toggle="popover" title="" data-content="Click here to pick all quantity" type="button" id="chkbox<?php echo $i.'-'. $selection->id; ?>" data-currentid="<?php echo $selection->current_id; ?> " data-id ="<?php echo $selection->id ?>" class="btn btn-default btn-number quickfill-remove"  ><span class="glyphicon glyphicon glyphicon-remove"></span></button>
                                        <button  type="button"  style="display: none;" class="btn btn-default btn-number quickfill-reset"  >RESET</button>
                                    </div>
                                </td>
                            </tr>
                            <?php  $i++; ?>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <div class="form-group responsive-button">
                    <input type="hidden" name="product_type" value="epm">
                    <input type="hidden" name="transfer_id" value="<?php echo $transfer->id; ?>">
                    <input type="button" class="btn btn-primary dosubmit" value="Submit" name="save">&nbsp;  <input type="button" class="btn btn-primary dosave" value="Save Draft" name="save">&nbsp;   <a href="javascript:history.go(-1)" class="btn ">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>