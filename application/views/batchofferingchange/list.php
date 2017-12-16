<?php

function is_temporary($data, $attribute_id)
{
    if (isset($data[$attribute_id]['commit_type']) && $data[$attribute_id]['commit_type'] == '0')
    {
        return true;
    }

    return false;
}

?>
<table class="table table-striped" id="config_items">
    <thead>
        <tr>
            <th><input type="checkbox" data-select-all name="allselect" /> </th>
            <th>Kiosk</th>
            <th>Location</th>
            <th>Pos</th>
            <th>SKU</th>
            <th>Name</th>
            <th>Aroma Price</th>
            <th>Kiosk Price</th>
            <th>Capacity</th>
            <th>PAR</th>
            <th>On Hand</th>
            <th>Missing</th>
            <th>Avail. W/H SOH</th>
            <th>Parts</th>
        </tr>
    </thead>
    <tbody>
        <?php if(is_array($data) > 0 ): ?>
            <?php $status_queued = $data['status_queued']; ?>
            <?php unset($data['status_queued']); ?>
            <?php foreach($data as $kid => $kioskData): ?>
                <?php foreach($kioskData as $kioskStatusData): ?>

                    <?php
                        $allocation = isset($kioskStatusData['Active'])?$kioskStatusData['Active']:"";
                        $queued = null;

                        if (isset($kioskStatusData['Queued'])) {
                            $queued = $kioskStatusData['Queued'];
                        }

                        $number = isset($allocation['number'])?$allocation['number']:(isset($queued['number'])?$queued['number']:"");
                        $name = isset($allocation['name'])?$allocation['name']:(isset($queued['name'])?$queued['name']:"");
                        $position = isset($allocation['position'])?$allocation['position']:(isset($queued['position'])?$queued['position']:"");
                        $id = isset($allocation['id'])?$allocation['id']:(isset($queued['id'])?$queued['id']:"");

                        if( isset($allocation['queue_type']) == false)
                        {
                            $allocation['queue_type'] = 'none';
                        }

                        if($allocation['queue_type'] == 'delete'){
                            unset(  $queued);
                            $queued['product'] = "<div><span class='unqueue-label queued'>&#8826;QUEUED TO BE REMOVED&#8827;</span><div>";
                        }
                        // print_r($kioskStatusData);
                    ?>
            
                    <?php if ($status_queued != 1 && !empty($kioskStatusData['Active'])): ?>
                        <tr rowspan="2">
                            <td>
                                <input type="checkbox" name="allocation[]" data-position='<?php echo $position; ?>' data-id='<?php echo $kid; ?>' value="<?php echo $kid; ?>" class="allocation_check offering-checkboxes"> 
                            </td>
                            <td><?php echo $number; ?></td>
                            <td><?php echo $name; ?></td>
                            <td><?php echo $position; ?></td>
                            <td class="<?php if (is_temporary($kioskStatusData['Active'], 1)) print 'font-italic'; ?>">
                                <?php echo isset($allocation['sku-value'] )? $allocation['sku-value']: ""; ?>
                            </td>
                            <td class="<?php if (is_temporary($kioskStatusData['Active'], 1)) print 'font-italic'; ?>">
                                <?php echo isset($allocation['product'])?$allocation['product']:""; ?>
                            </td>
                            <td class="<?php if (is_temporary($kioskStatusData['Active'], 2)) print 'font-italic'; ?>">
                                <?php echo !empty($allocation['price'])?$allocation['price']:"" ?>
                            </td>
                            <td>
                                <?php echo !empty($allocation['price3'])?$allocation['price3']:"" ?>
                            </td>
                            <td class="<?php if (is_temporary($kioskStatusData['Active'], 5)) print 'font-italic'; ?>">
                                <?php echo isset($allocation['capacity'])?$allocation['capacity']:""; ?>
                            </td>
                            <td class="<?php if (is_temporary($kioskStatusData['Active'], 6)) print 'font-italic'; ?>">
                                <?php echo isset($allocation['par'])?$allocation['par']:""; ?>
                            </td>
                            <td class="<?php if (is_temporary($kioskStatusData['Active'], 4)) print 'font-italic'; ?>">
                                <?php echo isset($allocation['on_hand'])?$allocation['on_hand']:""; ?>
                            </td>
                            <td>
                                <?php
                                    if(isset($allocation['par'])==false) $allocation['par']=0;
                                    if(isset($allocation['on_hand'])==false) $allocation['on_hand']=0;
                                    echo intval($allocation['par']) - intval($allocation['on_hand']);
                                ?>
                            </td>
                            <td>
                                <?php echo $allocation['SOH']; ?>
                            </td>
                            <td>
                                <div class="<?php if (is_temporary($kioskStatusData['Active'], 9)) print 'font-italic'; ?>"><?php echo isset($allocation['coil'])?$allocation['coil']:""; ?></div>
                                <div class="<?php if (is_temporary($kioskStatusData['Active'], 10)) print 'font-italic'; ?>"><?php echo isset($allocation['pusher'])?$allocation['pusher']:""; ?></div>
                                <div class="<?php if (is_temporary($kioskStatusData['Active'], 11)) print 'font-italic'; ?>"><?php echo isset($allocation['stabiliser'])?$allocation['stabiliser']:""; ?></div>
                                <div class="<?php if (is_temporary($kioskStatusData['Active'], 12)) print 'font-italic'; ?>"><?php echo isset($allocation['platform'])?$allocation['platform']:""; ?></div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    
                    <?php  if (count($queued)): ?>
                        <tr rowspan="2" style="background: #e1f7de">
                            <td>
                                <input type="checkbox" name="allocation[]" data-position='<?php echo $position; ?>' data-id='<?php echo $kid; ?>' value="<?php echo $kid; ?>" class="allocation_check offering-checkboxes">
                            </td>
                            <td><?php echo $number; ?></td>
                            <td><?php echo $name; ?></td>
                            <td><?php echo $position; ?></td>
                            <td class="<?php if (is_temporary($queued, 1)) print 'font-italic'; ?>">
                                <?php echo (isset($queued['sku-value'])?$queued['sku-value']:""); ?>
                            </td>
                            <td class="<?php if (is_temporary($queued, 1)) print 'font-italic'; ?>">
                                <?php echo (isset($queued['product'])?$queued['product']:""); ?>
                            </td>
                            <td class="<?php if (is_temporary($queued, 2)) print 'font-italic'; ?>">
                                <?php echo (!empty($queued['price'])?$queued['price']:""); ?>
                            </td>
                            <td></td>
                            <td class="<?php if (is_temporary($queued, 5)) print 'font-italic'; ?>">
                                <?php if (isset($queued['capacity'])) echo $queued['capacity']; ?>
                            </td>
                            <td class="<?php if (is_temporary($queued, 6)) print 'font-italic'; ?>">
                                <?php echo (isset($queued['par'])?$queued['par']:""); ?>
                            </td>
                            <td class="<?php if (is_temporary($queued, 4)) print 'font-italic'; ?>">
                                <?php echo (isset($queued['on_hand'])? $queued['on_hand']:""); ?>
                            </td>
                            <td></td>
                            <td>
                                <?php if (!empty($queued['sku-value'])) print $queued['SOH']; ?>
                            </td>
                            <td>
                                <div class="<?php if (is_temporary($queued, 9)) print 'font-italic'; ?>"><?php echo isset($queued['coil'])?$queued['coil']:""; ?></div>
                                <div class="<?php if (is_temporary($queued, 10)) print 'font-italic'; ?>"><?php echo isset($queued['pusher'])?$queued['pusher']:""; ?></div>
                                <div class="<?php if (is_temporary($queued, 11)) print 'font-italic'; ?>"><?php echo isset($queued['stabiliser'])?$queued['stabiliser']:""; ?></div>
                                <div class="<?php if (is_temporary($queued, 12)) print 'font-italic'; ?>"><?php echo isset($queued['platform'])?$queued['platform']:""; ?></div>
                                <?php if (!empty($queued['label_value'])): ?>
                                    <?php list($attribute_option_id, $value) = explode('|', $queued['label_value']); ?>
                                    <div class="<?php if (is_temporary($queued, 13)) print 'font-italic'; ?>"><?php echo $value == 1 ? 'New Label Required':'New Label Not Required'; ?></div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<div id="bulk-buttons" style="display: none;">
    <div class="btn-group" role="group" aria-label="...">
        <button role="group" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Bulk Actions <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li><a href="#" id="commit-position">Commit Selected</a></li>
            <li><a href="#" id="batch-modify">Modify Selected</a></li>
            <li><a href="#" id="remove-selected" >Remove Selected</a></li>
            <li><a href="#" id="unqueue-position">Unqueue Selected</a></li>
        </ul>
    </div>
    <div class="btn-group" role="group" aria-label="...">
        <button type="button" id="add-position" class="btn btn-default"><i class="fa fa-plus" aria-hidden="true"></i> Add Position</button>
    </div>
</div>
