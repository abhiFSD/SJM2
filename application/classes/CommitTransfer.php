<?php

namespace POW\Classes;

class CommitTransfer
{
    public static function run($post)
    {
        $transfer_id = $post['transfer_id'];
        $transfer = \POW\Transfer::with_id($transfer_id);

        if ($transfer->location_from_type == 'inventory_location' && $transfer->location_to_type == 'kiosk')
        {
            if (!empty($post['offering']))
            {
                foreach($post['offering']['newsoh'] as $position => $newsoh)
                {
                    if ($newsoh == 0) continue;
                    
                    $sku_id = $post['offering']['sku'][$position];
                    $picked_quantity = intval($post['offering']['picked'][$position]);
                    $kiosk_soh = $post['offering']['soh'][$position];

                    self::warehouse_to_kiosk_update_soh(intval($newsoh), $position, $sku_id, $transfer, $picked_quantity, $kiosk_soh);
                }
            }

            if (!empty($post['planagram_change']))
            {
                if (!empty($post['return_to_warehouse']))
                {
                    self::return_to_warehouse($post, $transfer);
                }

                foreach ($post['planagram_change'] as $position => $attribute_ids)
                {
                    \POW\OfferingAttributeAllocation::commit_changes_in_position(get_instance()->session->userdata('user_id'), $transfer->location_to_id, $position, $attribute_ids);
                }
            }
        }

        if ($transfer->location_from_type == 'kiosk' && $transfer->location_to_type == 'inventory_location')
        {
            if (!empty($post['transfer_stock']))
            {
                foreach ($post['transfer_stock']['sku_id'] as $position => $sku_id)
                {
                    $amount = intval($post['transfer_stock']['amount'][$position]);
                    
                    if ($amount)
                    {
                        self::kiosk_to_warehouse_update_soh($position, $sku_id, $transfer, $amount);
                    }
                }
            }
        }

        $transfer->status = 'complete';
        $transfer->save();

        $transfer_status = new \POW\TransferStatus();
        $transfer_status->date_created = date('Y-m-d H:i:s');
        $transfer_status->transfer_id = $transfer_id;
        $transfer_status->status = 'Fully Allotted';
        $transfer_status->user_id = get_instance()->session->userdata('user_id');
        $transfer_status->save();
    }

    public static function warehouse_to_kiosk_update_soh($newsoh, $position, $sku_id, $transfer, $picked_quantity, $kiosk_soh)
    {
        // special case, avoid race condition
        \POW\BaseModel::getdb()
            ->set('value', $newsoh)
            ->set('user_applied', get_instance()->session->userdata('user_id'))
            ->where('kiosk_id', $transfer->location_to_id)
            ->where('position', $position)
            ->where('offering_attribute_id', 4)
            ->update('offering_attribute_allocation');

        $warehouse_soh = \POW\InventoryItem::unadjusted_warehouse_soh($transfer->location_from_id, $sku_id);
        $new_warehouse_soh = $warehouse_soh - $picked_quantity;
        $new_kiosk_soh =  $kiosk_soh + $picked_quantity;
    
        if ($picked_quantity)
        {
            // special case, avoid race condition
            \POW\BaseModel::getdb()
                ->set('SOH', 'SOH - '.$picked_quantity, false)
                ->where('sku_id', $sku_id)
                ->where('location_id', $transfer->location_from_id)
                ->update('inventory_item');

            $inventory_item = \POW\InventoryItem::warehouse_sku($transfer->location_from_id, $sku_id);

            self::log('inventory_location', $transfer->location_from_id, $sku_id, $picked_quantity, $new_warehouse_soh, 'Pick', -1, $inventory_item->position);
            self::log('kiosk', $transfer->location_to_id, $sku_id, $picked_quantity, $new_kiosk_soh, 'Replenishment', 1, $position);
        }

        if ($newsoh - $picked_quantity != $kiosk_soh)
        {
            $adjustment = $newsoh - $picked_quantity - $kiosk_soh;

            self::log('kiosk', $transfer->location_to_id, $sku_id, $adjustment, $newsoh, "Stocktake", 1, $position);  
        }
    }

    public static function kiosk_to_warehouse_update_soh($position, $sku_id, $transfer, $picked_quantity)
    {
        if (\POW\InventoryItem::sku_exists($transfer->location_to_id, $sku_id))
        {
            $warehouse_soh = \POW\InventoryItem::unadjusted_warehouse_soh($transfer->location_to_id, $sku_id);
            $new_warehouse_soh = $warehouse_soh + $picked_quantity;

            // special case, avoid race condition
            \POW\BaseModel::getdb()
                ->set('SOH', 'SOH + '.$picked_quantity, false)
                ->where('sku_id', $sku_id)
                ->where('location_id', $transfer->location_to_id)
                ->update('inventory_item');
        }
        else
        {
            $datetime = date('Y-m-d H:i:s');
            $new_warehouse_soh = $picked_quantity;

            // special case, avoid race condition
            \POW\BaseModel::getdb()
                ->set('SOH', $picked_quantity)
                ->set('sku_id', $sku_id)
                ->set('location_id', $transfer->location_to_id)
                ->set('date_created', $datetime)
                ->set('date_updated', $datetime)
                ->insert('inventory_item');
        }

        $inventory_item = \POW\InventoryItem::warehouse_sku($transfer->location_to_id, $sku_id);

        self::log('kiosk', $transfer->location_from_id, $sku_id, $picked_quantity, 0, 'Warehouse Returns', -1, $position);
        self::log('inventory_location', $transfer->location_to_id, $sku_id, $picked_quantity, $new_warehouse_soh, 'Transfer', 1, $inventory_item->position);
    }

    public static function log($location_type, $location_id, $item_id, $adjustment_amount, $soh, $adjustment_type, $direction, $position)
    {
        if ($adjustment_amount !=0)
        {
            $datetime = date('Y-m-d H:i:s');
            
            $stock_movement_log = new \POW\StockMovementLog();
            $stock_movement_log->location_type = $location_type;
            $stock_movement_log->location_id = $location_id;
            $stock_movement_log->item_id = $item_id;
            $stock_movement_log->adjustment_amount = ($direction * $adjustment_amount > 0 ? '+' : '').($direction * $adjustment_amount);
            $stock_movement_log->SOH = $soh;
            $stock_movement_log->adjustment_type = $adjustment_type;
            $stock_movement_log->adjustment_date = $datetime;
            $stock_movement_log->date_created = $datetime;
            $stock_movement_log->date_updated = $datetime;
            $stock_movement_log->description = $position;
            $stock_movement_log->user_id = get_instance()->session->userdata('user_id');
            $stock_movement_log->save();
        }
    }

    public static function return_to_warehouse($post, $current_transfer)
    {
        $ci = get_instance();
        $datetime = date('Y-m-d H:i:s');
        $user_id = $ci->session->userdata('user_id');
        $user = \POW\User::with_id($user_id);

        if (!$user->inventory_location_id)
        {
            $ci->session->set_flashdata('warning_message', 'You don\'t have a default warehouse, using '.$current_transfer->location_from->name);
        }

        $transfer = new \POW\Transfer();
        $transfer->submitter = $ci->session->userdata('user_id');
        $transfer->location_from_type = 'kiosk';
        $transfer->location_to_type = 'inventory_location';
        $transfer->location_from_id = $current_transfer->location_to_id;
        $transfer->location_to_id = $user->inventory_location_id ? $user->inventory_location_id : $current_transfer->location_from_id;
        $transfer->date_created = $datetime;
        $transfer->status = 'Transferred';
        $transfer->save();

        $transfer_status = new \POW\TransferStatus();
        $transfer_status->date_created = $datetime;
        $transfer_status->transfer_id = $transfer->id;
        $transfer_status->status = 'Job Created';
        $transfer_status->location_type = $transfer->location_from_type;
        $transfer_status->location_id = $transfer->location_from_id;
        $transfer_status->user_id = $user_id;
        $transfer_status->save();

        $transfer_status = new \POW\TransferStatus();
        $transfer_status->date_created = $datetime;
        $transfer_status->transfer_id = $transfer->id;
        $transfer_status->status = 'Pick Generated';
        $transfer_status->location_type = $transfer->location_from_type;
        $transfer_status->location_id = $transfer->location_from_id;
        $transfer_status->user_id = $user_id;
        $transfer_status->save();

        $transfer_status = new \POW\TransferStatus();
        $transfer_status->date_created = $datetime;
        $transfer_status->transfer_id = $transfer->id;
        $transfer_status->status = 'Fully Picked';
        $transfer_status->location_type = $transfer->location_from_type;
        $transfer_status->location_id = $transfer->location_from_id;
        $transfer_status->user_id = $user_id;
        $transfer_status->save();

        $transfer_status = new \POW\TransferStatus();
        $transfer_status->date_created = $datetime;
        $transfer_status->transfer_id = $transfer->id;
        $transfer_status->status = 'Transferred';
        $transfer_status->location_type = 'Staff';
        $transfer_status->location_id = $user->party_id;
        $transfer_status->user_id = $user_id;
        $transfer_status->save();

        $transfer_item_count = 0;

        $swapped_total = [];
        if (!empty($post['swap']))
        {
            foreach ($post['swap'] as $sku => $amounts)
            {
                $swapped_total[$sku] = 0;
                foreach ($amounts as $amount)
                {
                    $swapped_total[$sku] += $amount;
                }
            }
        }

        foreach ($post['return_to_warehouse']['soh'] as $position => $attribute)
        {
            foreach ($attribute as $attribute_id => $soh)
            {
                if (empty($soh)) continue;

                $sku_id = $post['return_to_warehouse']['sku'][$position];
                $swapped = empty($swapped_total[$sku_id]) ? 0 : $swapped_total[$sku_id];

                if ($swapped && $soh <= $swapped)
                {
                    $returned = 0;
                    $swapped_total[$sku_id] -= $soh;
                }
                elseif ($swapped && $soh > $swapped)
                {
                    $returned = $soh - $swapped;
                    $swapped_total[$sku_id] -= $swapped;
                }
                else
                {
                    $returned = $soh;
                }

                if (0 == $returned) continue;

                $transfer_item = new \POW\TransferItem();
                $transfer_item->offering_attribute_id = $attribute_id;
                $transfer_item->value = $sku_id;
                $transfer_item->pick_quantity = $returned;
                $transfer_item->picked_quantity = $returned;
                $transfer_item->transfer_id = $transfer->id;
                $transfer_item->position = $position;
                $transfer_item->date_created = $datetime;
                $transfer_item->save();

                $transfer_item_count++;
            }
        }

        if (!$transfer_item_count)
        {
            $transfer->delete();
        }        
    }

}
