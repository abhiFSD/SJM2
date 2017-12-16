<div class="row">
    <div class="col-md-12">
        <h2 class="page-header">Put Away</h2>
        <h3>
            <?php if ($transfer->location_to_type == 'kiosk'): ?>
                <?php print $transfer->location_to->number.' - '.$transfer->location_to->location_name; ?>
            <?php else: ?>
                <?php print $transfer->location_to->name; ?>
            <?php endif; ?>
        </h3>
 
        <h5>Job ID <?php echo $transfer->id; ?></h5>
    </div>
    <form name="pick-form" id="atkiosk-pick">
        <input type="hidden" name="kiosk_id" value="<?php echo $transfer->location_to_id;?>">
        <input type="hidden" name="transfer_id" value="<?php echo $transfer->id ?>">
        <div class="col-md-12">

            <?php if($transfer->location_to_type == 'kiosk' && !empty($selections)): ?>
                <h4>Queued Kiosk Attribute Changes </h4>
                <table id="table-product-s" class="table table-bordered actions">
                    <thead>
                        <tr>
                            <th>Attribute</th>
                            <th>Current Value</th>
                            <th>Queued Value</th>
                            <th>Complete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 0; ?>
                        <?php if(isset($selections) && $selections): ?>
                            <?php foreach($selections as $selection): ?>
                                <tr class="value_rows">
                                    <td><input type ="text" class="no-show-input" value="<?php echo $selection->name; ?>" readonly/></td>
                                    <td> <div id="values_queued"> <?php echo $selection->current_value; ?> </div> </td>
                                    <td> <?php echo $selection->value; ?> </td>
                                    <td>             
                                        <div class="input-group quickselect">
                                            <span style="display: none;" class="label label-success">Done</span>
                                            <button   data-html="false"  data-toggle="popover"  data-content="Click here to pick all quantity" type="button" id="chkbox<?php echo $i.'-'. $selection->id; ?>" data-currentid="<?php echo $selection->current_id; ?> " data-id ="<?php echo $selection->id ?>" class="btn btn-default btn-number quickfill-commit"  ><span class="glyphicon glyphicon-ok"></span></button>
                                            <span style="display: none;" class="label label-default">Not Done</span>
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

            <?php if ($transfer->location_to_type == 'kiosk'): ?>
                <?php $this->load->view('picks/sections/atkiosk_allotment_kiosk', ['items' => $items, 'transfer' => $transfer]); ?>
            <?php elseif ($transfer->location_to_type == 'inventory_location'): ?>
                <?php $this->load->view('picks/sections/atkiosk_allotment_warehouse', ['transfer_items' => $transfer_items, 'transfer' => $transfer]); ?>
            <?php endif; ?>

            <div class="single-kiosk-table">
                <?php if ($transfer->location_to_type == 'kiosk'): ?>
                    <h4 style="display: inline; margin-bottom: 20px;">Price Issues</h4>
                    <input type="button" id="check-price" class="btn btn-primary dosubmits" value="Check Price"/" name="save">
                    <table id="table-check-price" class="table table-bordered actions" style="display: <?php echo count($price_changes)>0 ? "block":"none" ?>">
                        <thead>
                            <tr>
                                <th>Position</th>
                                <th>Last Dex</th>
                                <th>Dex/Kiosk Price</th>
                                <th>Aroma Price</th>
                            </tr>
                        </thead>
                        <tbody id="check-price-data">
                            <?php foreach ($price_changes as $key2 => $p): ?>
                                <tr>
                                    <td scope="row"><?php echo $p['position'] ?></td>
                                    <td><?php echo $p['last_dex_time'] ?> </td>
                                    <td><?php echo $p['dexprice'] ?></td>
                                    <td scope="row"><?php echo $p['price'] ?> </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                <div class="form-group responsive-button">
                    <input type="button" class="btn btn-primary dosubmit" value="Submit" name="save" data-url="<?php print site_url('batchofferingchange/ProcessOfferingAttributes'); ?>">&nbsp;  
                    <input type="button" class="btn btn-primary dosave" value="Save Draft" name="save" data-url="<?php print site_url('batchofferingchange/ProcessOfferingAttributesSaveDraft'); ?>">&nbsp;   
                    <a href="javascript:history.go(-1)" class="btn ">Cancel</a>
                </div>
            </div>

        </form>
    </div>
</div>

<?php if ($transfer->location_to_type == 'kiosk'): ?>
    <input type="hidden" id="atKioskPriceChanges" name="price_changes" value="<?php echo count($price_changes) ?>">
<?php endif; ?>
