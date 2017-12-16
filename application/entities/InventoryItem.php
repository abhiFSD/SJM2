<?php

namespace POW;

class InventoryItem extends BaseModel
{
    protected static $table_name = 'inventory_item';

    protected static $fillable = [
        'id',
        'sku_id',
        'location_id',
        'sub_location',
        'SOH',
        'batch_id',
        'date_created',
        'date_updated',
        'position',
    ];

    public static function sku_exists($warehouse_id, $sku_id)
    {
        $query = self::getdb()
            ->where('location_id', $warehouse_id)
            ->where('sku_id', $sku_id)
            ->get(self::$table_name);

        return $query && $query->num_rows() ? true : false;
    }

    public static function unadjusted_warehouse_soh($warehouse_id, $sku_id)
    {
        $query = self::getdb()
            ->select('SOH')
            ->where('location_id', $warehouse_id)
            ->where('sku_id', $sku_id)
            ->get(self::$table_name);

        return $query && $query->num_rows() ? $query->row()->SOH : 0;
    }

    public static function warehouse_sku($warehouse_id, $sku_id)
    {
        $query = self::getdb()
            ->where('location_id', $warehouse_id)
            ->where('sku_id', $sku_id)
            ->get(self::$table_name);

        return self::row($query);        
    }

    public static function adjusted_sku_soh($warehouse_id, $sku_id)
    {
        $options = new \stdClass();
        $options->warehouse_id = $warehouse_id;
        $options->sku_id = $sku_id;

        $query = self::warehouse_soh($options, $return_query = true);

        return $query && $query->num_rows() ? $query->row()->available_soh : 0;
    }

    public static function warehouse_soh($options, $return_query = false)
    {
        /** get quantities for transfers **/
        $transfers = self::getdb()
            ->select('transfer_item.value')
            ->select('transfer.location_from_id')
            ->select('transfer_item.offering_attribute_id')
            ->select('transfer_item.pick_quantity')
            ->from('transfer_item')
            ->join('transfer', "transfer.id = transfer_item.transfer_id AND transfer.status <> 'complete'")
            ->where('offering_attribute_id', 1)
            ->get_compiled_select();

        //============================ do not cross-contaminate query builders

        /** aggregate quantities by warehouse subtracting transfers **/
        $builder = self::getdb()
            // sku must be joined before item
            ->join('sku', 'sku.id = inventory_item.sku_id');

        if (!empty($options->sku_id))
        {
            $builder->where('inventory_item.sku_id', $options->sku_id);
        }
        if (!empty($options->warehouse_id) && $options->warehouse_id != 'All')
        {
            $builder->where('inventory_item.location_id', $options->warehouse_id);
        }
        if (!empty($options->warehouse_ids))
        {
            $builder->where_in('inventory_item.location_id', $options->warehouse_ids);
        }
        if (!empty($options->available_only))
        {
            $builder->having('available_soh >', 0);
        }
        if (!empty($options->item_category_type))
        {
            $builder
                ->join('item', 'sku.product_id = item.id')
                ->where_in('item.product_type', $options->item_category_type);
        }
        if (!empty($options->order_by))
        {
            foreach ($options->order_by as $order_pair) 
            {
                $builder->order_by($order_pair[0], $order_pair[1]);
            }
        }

        $builder
            ->select('sku.id skuid')
            ->select('sku.sku_value')
            ->select('sku.name')
            ->select('inventory_item.position')
            ->select('inventory_location.name warehouse_name')
            ->select('(IFNULL(inventory_item.SOH, 0) - SUM(IFNULL(transfers.pick_quantity, 0))) available_soh', false)
            ->select('SUM(IFNULL(transfers.pick_quantity, 0)) allocated_soh', false)
            ->select('IFNULL(inventory_item.SOH, 0) total_soh', false)
            ->from('inventory_item')
            ->join("($transfers) as transfers", "transfers.value = inventory_item.sku_id AND transfers.location_from_id = inventory_item.location_id AND transfers.offering_attribute_id = 1", 'left')
            ->join('inventory_location', 'inventory_location.id = inventory_item.location_id')
            ->group_by('inventory_item.sku_id')
            ->group_by('inventory_item.location_id')
            ->order_by('sku.sku_value', 'asc');

        //============================ do not cross-contaminate query builders

        if (!empty($options->group_by_warehouse))
        {
            $query = $builder->get();
        }
        elseif (!empty($options->return_warehouse_builder))
        {
            return $builder;
        }
        else
        {
            $inner_select = $builder->get_compiled_select();

            /** aggregate quantities for all warehouses **/
            $query = self::getdb()->query("
                SELECT 
                    inventory.sku_value,
                    inventory.name,
                    SUM(inventory.available_soh) available_soh,
                    SUM(inventory.allocated_soh) allocated_soh,
                    SUM(inventory.total_soh) total_soh
                FROM ($inner_select) AS inventory
                GROUP BY sku_value, name
            ");
        }

        if ($return_query)
        {
            return $query;
        }

        return $query && $query->num_rows() ? $query->result() : [];
    }

    /**
     * Get inventory items in Stocktake Form based on selected location
     * @param stdClass object $options
     * @param bool $return_query
     * @return array
     */
    public static function stocktake_form_products($options, $return_query = false)
    {
        $builder = self::getdb()
            ->select('sku.id')
            ->select('sku.sku_value')
            ->select('sku.name')
            ->select('inventory_item.SOH')
            ->select('inventory_item.position')
            ->from('sku')
            ->join('inventory_item', 'inventory_item.sku_id = sku.id')
            ->join('item', 'item.id = sku.product_id');

        if (isset($options->id))
        {
            $builder->where('inventory_item.location_id', $options->id);
        }

        if (trim($options->type) != "")
        {
            $builder->where_in('item.product_type', explode(',', $options->type));
        }

        if (isset($options->order_by))
        {
            $direction = isset($options->order_direction) ? $options->order_direction : 'ASC';
            $builder->order_by($options->order_by, $direction);
        }
        else
        {
            $builder->order_by('inventory_item.position', 'ASC');
        }

        $query = $builder->get();

        if ($return_query)
        {
            return $query;
        }

        return $query && $query->num_rows() ? $query->result_array() : [];
    }

}
