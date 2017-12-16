<?php

namespace POW;

class TransferItem extends BaseModel
{
    protected static $table_name = 'transfer_item';

    protected static $fillable = [
        'id',
        'position',
        'offering_attribute_id',
        'value',
        'pick_quantity',
        'pick_short_quantity',
        'picked_quantity',
        'date_updated',
        'date_created',
        'transfer_id',
        'in_kiosk',
    ];

    protected static $decoration = [
        'sku_name',
        'product_type',
        'warehouse_position'
    ];

    //==================================================
    // Static Methods
    //==================================================
    
    public static function with_transfer_id($transfer_id)
    {
        $inventory_item_join = "inventory_item.sku_id = sku.id AND 
            ((inventory_item.location_id = transfer.location_from_id AND transfer.location_from_type = 'inventory_location') 
            OR (inventory_item.location_id = transfer.location_to_id AND transfer.location_to_type = 'inventory_location'))";

        $query = self::getdb()
            ->select(self::$table_name.'.*')
            ->select('sku.name sku_name')
            ->select('item.product_type')
            ->select('inventory_item.position warehouse_position')
            ->join('transfer', 'transfer.id = transfer_item.transfer_id', 'inner')
            ->join('sku', 'sku.id = transfer_item.value', 'left')
            ->join('item', 'item.id = sku.product_id', 'left')
            ->join('inventory_item', $inventory_item_join, 'left', false)
            ->where('transfer_id', $transfer_id)
            ->order_by('position', 'asc')
            ->get(self::$table_name);

        return self::listify($query);
    }

    public static function first_with_transfer_id($transfer_id)
    {
        $query = self::getdb()
            ->select('transfer_id', $transfer)
            ->order_by('date_created', 'asc')
            ->get(self::$table_name);

        return self::row($query);
    }

    public static function set_in_kiosk($id)
    {
        return self::getdb()
            ->where('id', $id)
            ->set('in_kiosk', 'Yes')
            ->update(self::$table_name);
    }

    public static function has_pick($kiosk_id, $position, $offering_attribute_id)
    {
        $query = self::getdb()
            ->join('transfer', self::$table_name.'.transfer_id = transfer.id')
            ->where('location_to_id', $kiosk_id)
            ->where('location_to_type', 'kiosk')
            ->where('position', $position)
            ->where('offering_attribute_id', $offering_attribute_id)
            ->where('pick_quantity >', 0)
            ->where('status NOT LIKE', 'complete')
            ->get(self::$table_name);

        return $query && $query->num_rows() ? true : false;
    }

    public static function with_sku_id($transfer_id, $kiosk_id, $sku_id)
    {
        $query = self::getdb()
            ->select(self::$table_name.'.*')
            ->join('transfer', self::$table_name.'.transfer_id = transfer.id')
            ->where('offering_attribute_id', 1)
            ->where('value', $sku_id)
            ->where('transfer_id', $transfer_id)
            ->where('location_to_id', $kiosk_id)
            ->where('location_to_type', 'kiosk')
            ->get(self::$table_name);

        return self::listify($query);
    }

    public static function get_picks($transfer, $transfers_only = false)
    {
        $new_data = [];

        $query = self::getdb()
            ->select('sku.name')
            ->select('sku.id as skuid')
            ->select('sku.sku_value')
            ->select('item.product_type')
            ->select('transfer_item.id transfer_item_id')
            ->select('transfer_item.position')
            ->select('transfer_item.picked_quantity')
            ->select('transfer_item.pick_quantity')
            ->select('transfer_item.in_kiosk')
            ->select('transfer_item.offering_attribute_id')
            ->select("IF(queued_sku.id IS NULL, oaa_soh.value, '0') soh", false)

            ->join('transfer', 'transfer.id = transfer_item.transfer_id')
            ->join('sku', 'sku.id = transfer_item.value', 'left')
            ->join('item', 'item.id = sku.product_id', 'left')
            ->join('offering_attribute_allocation AS oaa_soh', "
                oaa_soh.kiosk_id = transfer.location_to_id 
                AND oaa_soh.offering_attribute_id = 4 
                AND oaa_soh.position = transfer_item.position
                AND transfer_item.offering_attribute_id = 1", 'left')
            ->join('offering_attribute_allocation AS queued_sku', "
                queued_sku.kiosk_id = transfer.location_to_id 
                AND queued_sku.offering_attribute_id = 1 
                AND queued_sku.status = 'Queued' 
                AND queued_sku.position = transfer_item.position
                AND transfer_item.offering_attribute_id = 1", 'left')

            ->where('transfer_id', $transfer->id)
            ->get('transfer_item');

        $new_data = array_merge($new_data, $query && $query->num_rows() ? $query->result() : []);

        if (!$transfers_only)
        {
            $transfer_items_select = self::getdb()
                ->select('position')
                ->where('transfer_id', $transfer->id)
                ->where('offering_attribute_id', 1)
                ->from('transfer_item')
                ->get_compiled_select();

            $query = self::getdb()
                ->select('sku.name')
                ->select('sku.id skuid')
                ->select('sku.sku_value')
                ->select('item.product_type')
                ->select('offering_attribute_allocation.position')
                ->select("'0' picked_quantity")
                ->select("'0' pick_quantity")
                ->select("'No' in_kiosk")
                ->select('offering_attribute_allocation.offering_attribute_id')
                ->select('IFNULL(oaa_soh.value, 0) soh', false)
                ->select('offering_attribute_allocation.status isnew')
                
                ->join('offering_attribute_allocation AS oaa_soh', '
                    oaa_soh.kiosk_id = offering_attribute_allocation.kiosk_id 
                    AND oaa_soh.offering_attribute_id = 4 
                    AND oaa_soh.position = offering_attribute_allocation.position', 'left')
                ->join('sku', 'sku.id = offering_attribute_allocation.value', 'left')
                ->join('item', 'item.id = sku.product_id', 'left')

                ->where_in('offering_attribute_allocation.offering_attribute_id', [1, 9, 10, 11, 12])
                ->where("offering_attribute_allocation.position NOT IN ($transfer_items_select)", null, false)
                ->where('offering_attribute_allocation.kiosk_id', $transfer->location_to_id)
                ->where('offering_attribute_allocation.status', 'Active')
                ->get('offering_attribute_allocation');

            $new_data = array_merge($new_data, $query && $query->num_rows() ? $query->result() : []);
        }

        usort($new_data, function($a, $b) {
            return $a->position == $b->position ? 0 : ($a->position < $b->position ? -1 : 1);
        });

        return $new_data;
    }

}

