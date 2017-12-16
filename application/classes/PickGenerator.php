<?php

namespace POW\Classes;

class PickGenerator
{
    protected static $current_stock = [];

    public static function run($transfer, $user_id, $allocation_type)
    {
        // \POW\Transfer::delete_most_recent_generated_pick($kiosk_id);

        $transfer->fill_level = $allocation_type;
        $transfer->status = 'Pick Generated';
        $transfer->save();

        $transfer_status = new \POW\TransferStatus();
        $transfer_status->date_created = date('Y-m-d H:i:s');
        $transfer_status->transfer_id = $transfer->id;
        $transfer_status->status = 'Pick Generated';
        $transfer_status->location_type = $transfer->location_from_type;
        $transfer_status->location_id = $transfer->location_from_id;
        $transfer_status->user_id = $user_id;
        $transfer_status->save();

        $ci = get_instance();
        $location = \POW\KioskLocation::with_kiosk_id($transfer->location_to_id);
        $current_stock = [];
        $transfer_items_count = 0;

        $ci->load->model('Picks_Model');

        // GET ALL POSITION BY KIOSK ID
        $positions = \POW\OfferingAttributeAllocation::getKioskPositions($transfer->location_to_id);
        $positions_with_queued_sku_available_soh = \POW\OfferingAttributeAllocation::positions_with_queued_sku_available_soh($transfer->location_to_id, $transfer->location_from_id);
        $positions_with_transfer_item = [];
        self::$current_stock = [];

        // it's more probable that queued sku changes will populate swap inventory, so process first
        foreach ($positions_with_queued_sku_available_soh as $position)
        {
            $status = self::generate_transfer_item($transfer, $position);

            if ($status)
            {
                $transfer_items_count++;
                $positions_with_transfer_item[] = $position;
            }
        }

        $positions = array_diff($positions, $positions_with_transfer_item);

        // cycle through non-queued positions
        foreach ($positions as $position)
        {
            $status = self::generate_transfer_item($transfer, $position);

            if ($status)
            {
                $transfer_items_count++;
            }
        }

        // cycle a third time to make sure swaps are counted
        if (!empty(self::$current_stock))
        {
            foreach (self::$current_stock as $sku_id => $onhand_in_kiosk)
            {
                foreach (\POW\TransferItem::with_sku_id($transfer->id, $transfer->location_to_id, $sku_id) as $transfer_item)
                {
                    if ($transfer_item->pick_quantity <= $onhand_in_kiosk)
                    {
                        $onhand_in_kiosk -= $transfer_item->pick_quantity;

                        $transfer_item->pick_quantity = 0;
                        $transfer_item->save();
                        
                        $transfer_items_count--;
                    }
                    else
                    {
                        $transfer_item->pick_quantity -= $onhand_in_kiosk;
                        $transfer_item->save();

                        $onhand_in_kiosk = 0;
                    }

                    if ($onhand_in_kiosk == 0) break;
                }
            }
        }

        // NO Transfer Item, so delete the transfer
        if ($transfer_items_count <= 0)
        {
            $transfer->delete();
        }
        else
        {
            $transfer_id = $transfer->id;
        }

        return $transfer_items_count;
    }

    protected static function generate_transfer_item($transfer, $position)
    {
        $ci = get_instance();
        $queued_attributes = null;
        $transfer_items_count = 0;

        $queued_items = $ci->Picks_Model->getAllQueuedItem($transfer->location_to_id, $position);
        if (!$queued_items) $queued_items = array();

        // get queued and current items by position
        $all_items = $ci->Picks_Model->getAllItem($transfer->location_to_id, $position);
        
        if (!count($all_items)) return;
        
        $all_attributes = self::getAttributes($position, $all_items);

        if (count($queued_items) > 0)
        {
            $queued_attributes = self::getAttributes($position, $queued_items);
        }

        $active_items = $ci->Picks_Model->getAllActiveItem($transfer->location_to_id, $position);
        if (!$active_items) return;
        if (count($active_items) > 0)
        {
            $active_attributes = self::getAttributes($position, $active_items);
        }

        if (isset($active_attributes[1]['sku_id']) == false) return;

        $existing_SKU = $active_attributes[1]['sku_id'];
        $existing_capacity = empty($active_attributes[5]['capacity']) ? 0 : $active_attributes[5]['capacity'];
        $existing_PAR = empty($active_attributes[6]['par']) ? 0 : $active_attributes[6]['par'];
        $on_hand_in_kiosk = empty($active_attributes[4]['soh']) ? 0 : $active_attributes[4]['soh'];

        $replenish_active_product = false;

        // get soh for current item
        if (count($queued_attributes) > 0)
        foreach($queued_attributes as $k3 => $nq)
        {
            if (!empty($queued_attributes[$k3]['sku_id']))
            {
                $warehouse_soh = \POW\InventoryItem::adjusted_sku_soh($transfer->location_from_id, $queued_attributes[$k3]['sku_id']);
                // check warehouse soh
                if ($warehouse_soh <= 0)
                {
                    // check swap soh
                    if (empty(self::$current_stock[$queued_attributes[$k3]['sku_id']]))
                    {
                        $replenish_active_product = true;
                        break;
                    }
                }
            }
        }

        if ($replenish_active_product)
        {
            if (\POW\TransferItem::has_pick($transfer->location_to_id, $position, 1)) return;

            $status = self::ProcessProductPick($existing_SKU, $existing_capacity, $existing_PAR, $on_hand_in_kiosk, $transfer, $position);
            if ($status != 0)
            {
                $transfer_items_count++;
            }
        }
        else
        {
            $product_sku = 0;

            if (!empty($queued_attributes[1]['sku_id']))
            {
                if (\POW\TransferItem::has_pick($transfer->location_to_id, $position, 1)) return;

                $product_sku = $queued_attributes[1]['sku_id'];
                $product_capacity = empty($all_attributes[5]['capacity']) ? 0 : $all_attributes[5]['capacity'];
                $product_par = empty($all_attributes[6]['par']) ? 0 : $all_attributes[6]['par'];

                $status = self::ProcessProductPick($product_sku, $product_capacity, $product_par, 0, $transfer, $position);

                if ($status > 0)
                {
                    // track product swaps
                    if ($on_hand_in_kiosk)
                    {
                        if (!empty(self::$current_stock[$existing_SKU]))
                        {
                            self::$current_stock[$existing_SKU] += $on_hand_in_kiosk;
                        }
                        else
                        {
                            self::$current_stock[$existing_SKU] = $on_hand_in_kiosk;
                        }
                    }

                    $transfer_items_count++;
                }
            }
            else
            {
                if (\POW\TransferItem::has_pick($transfer->location_to_id, $position, 1)) return;

                $status = self::ProcessProductPick($existing_SKU, $existing_capacity, $existing_PAR, $on_hand_in_kiosk, $transfer, $position);

                if ($status > 0)
                {
                    $transfer_items_count++;
                }
            }

            if (count($queued_attributes) > 0)
            {
                foreach($queued_attributes as $attribute_id => $data)
                {
                    if (1 == $attribute_id) continue;

                    if (!empty($data['isPicked']))
                    {
                        $status = 0;

                        if (isset($data['sku_id']))
                        {
                            if (\POW\TransferItem::has_pick($transfer->location_to_id, $position, $attribute_id)) continue;

                            $status = self::ProcessNonProductItemPick($attribute_id, $data, $transfer->id, $transfer->location_from_id, $position);
                        }
                        else
                        {
                            $status = self::ProcessNonProductItemPick($attribute_id, $data, $transfer->id, 0, $position);
                        }

                        if ($status > 0)
                        {
                            $transfer_items_count++;
                        }
                    }
                }
            }
        }

        return $transfer_items_count;
    }

    private static function ProcessNonProductItemPick($offering_attribute_id, $attribute, $transfer_id, $warehouse, $position)
    {
        $transfer_item_id = 0;

        $transfer_item = new \POW\TransferItem();
        $transfer_item->offering_attribute_id = $offering_attribute_id;
        $transfer_item->transfer_id = $transfer_id;
        $transfer_item->position = $position;
        $transfer_item->date_created = date('Y-m-d H:i:s');

        // label
        if (empty($attribute['isItem']))
        {
            $transfer_item->pick_quantity = 1;
            $transfer_item->save();
            
            $transfer_item_id = $transfer_item->id;
        }
        // part
        else
        {
            $adjusted_soh = \POW\InventoryItem::adjusted_sku_soh($warehouse, $attribute['sku_id']);

            if ($adjusted_soh > 0)
            {
                $pick_short_quantity = 0;
                $pick_quantity = 1;
            }
            else
            {
                $pick_short_quantity = 1;
                $pick_quantity = 0;
            }

            $transfer_item->value = $attribute['sku_id'];
            $transfer_item->pick_quantity = $pick_quantity;
            $transfer_item->pick_short_quantity = $pick_short_quantity;
            $transfer_item->save();

            $transfer_item_id = $transfer_item->id;
        }

        return $transfer_item_id;
    }

    private static function ProcessProductPick($sku_id, $capacity, $par, $on_hand_in_kiosk, $transfer, $position)
    {
        $pick_required = ($transfer->fill_level == "par") ? ($par - $on_hand_in_kiosk) : ($capacity - $on_hand_in_kiosk);
        $pick_short_quantity = 0;
        $transfer_item_id = 0;
        $swap = 0;

        // substract swap stock first
        if (!empty(self::$current_stock[$sku_id]))
        {
            if ($pick_required <= self::$current_stock[$sku_id])
            {
                self::$current_stock[$sku_id] -= $pick_required;
                $swap = $pick_required;
                $pick_required = 0;
                $adjusted_soh = 0;
            }
            else
            {
                $pick_required -= self::$current_stock[$sku_id];
                $swap = self::$current_stock[$sku_id];
                self::$current_stock[$sku_id] = 0;
                $adjusted_soh = \POW\InventoryItem::adjusted_sku_soh($transfer->location_from_id, $sku_id);
            }
        }
        else
        {
            $adjusted_soh = \POW\InventoryItem::adjusted_sku_soh($transfer->location_from_id, $sku_id);
        }

        if ($pick_required <= $adjusted_soh)
        {
            $pick_quantity = $pick_required;
        }
        else
        {
            $pick_quantity = $adjusted_soh;
            $pick_short_quantity = $pick_required - $pick_quantity;
        }

        if ($pick_quantity > 0 || $pick_short_quantity > 0 || $swap)
        {
            $transfer_item = new \POW\TransferItem();
            $transfer_item->offering_attribute_id = 1;
            $transfer_item->value = $sku_id;
            $transfer_item->pick_quantity = $pick_quantity;
            $transfer_item->transfer_id = $transfer->id;
            $transfer_item->position = $position;
            $transfer_item->date_created = date('Y-m-d H:i:s');
            $transfer_item->pick_short_quantity = $pick_short_quantity;
            $transfer_item->save();

            $transfer_item_id = $transfer_item->id;
        }

        return $transfer_item_id;
    }

    private static function getAttributes($position, $attrs)
    {
        $result = array();
        foreach($attrs as $k => $a)
        {
            $id = $a['offering_attribute_id'];

            $result[$id]['isOptional'] = $a['isOptional'];
            $result[$id]['isItem'] = 0;
            $result[$id]['isPicked'] = 0;

            // GET SOH
            if ($id == 4)
            {
                $soh = $a['value'];
                $soh_id = $a['id'];
                $result['soh'] = $soh;
                $result['soh_id'] = $soh_id;
                $result[$id]['soh_id'] = $result['soh_id'];
                $result[$id]['soh'] = $result['soh'];
                $result[$id]['name'] = "soh";
            }

            // GET SKU_ID
            if ($id == 1)
            {
                $sku_id = $a['value'];
                $result[$id]['sku_id'] = $sku_id;
                $result[$id]['isItem'] = 1;
                $result[$id]['isPicked'] = 1;
                $result[$id]['name'] = "item";
            }

            if ($id == 5)
            {
                $result['capacity'] = $a['value'];
                $result[$id]['name'] = "capacity";
                $result[$id]['capacity'] = $result['capacity'];
            }

            if ($id == 6)
            {
                $result['par'] = $a['value'];
                $result[$id]['par'] = $result['par'];
                $result[$id]['name'] = "par";
            }

            if ($id == 9 || $id == 10 || $id == 11 || $id == 12)
            {
                $result[$id]['isItem'] = 1;
                $result[$id]['isPicked'] = 1;
                $result[$id]['sku_id'] = $a['value'];
            }

            if ($id == 9)
            {
                $result[$id]['name'] = "Coil";
            }

            if ($id == 10)
            {
                $result[$id]['name'] = "Pusher";
            }

            if ($id == 11)
            {
                $result[$id]['name'] = "Stabiliser";
            }

            if ($id == 12)
            {
                $result[$id]['name'] = "Platform";
            }

            if ($id == 13)
            {
                $result['label'] = $a['value'];
                $result[$id]['isPicked'] = 1;
                $result[$id]['name'] = "label";
            }

            if (isset($soh) == false)
            {
                $result[$id]['soh'] = 0;
            }
        }

        return $result;
    }

}
