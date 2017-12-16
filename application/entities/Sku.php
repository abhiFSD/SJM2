<?php

namespace POW;

class Sku extends BaseModel
{
    protected static $table_name = 'sku';

    protected static $fillable = [
        'id',
        'sku_value',
        'name',
        'product_id',
        'ean13_barcode',
        'status',
        'date_created',
        'date_updated',
    ];

    protected static $decoration = [
        'item_name',
    ];

    public static function get_all($options = null)
    {
        if (!empty($options->item_category_type))
        {
            self::getdb()
                ->join('item', 'item.id = sku.product_id')
                ->where_in('item.product_type', $options->item_category_type);
        }

        if (!empty($options->order))
        {
            self::getdb()->order_by(self::$table_name.'.'.$options->order, 'asc');
        }
        else
        {
            self::getdb()->order_by('sku_value', 'asc');
        }

        $query = self::getdb()
            ->select(self::$table_name.'.*')
            ->get(self::$table_name);

        return self::listify($query);
    }

    public static function with_category_id($category_id)
    {
        $query = self::getdb()
            ->select(self::$table_name.'.*')
            ->where('product_category_id', $category_id)
            ->join('item', self::$table_name.'.product_id = item.id')
            ->get(self::$table_name);

        return self::listify($query);
    }

    public static function get_sku_with_item($sku_id,$fields = NULL)
    {
        $builder = self::getdb();
        $builder->join('item', 'item.id = sku.product_id')
            ->where('sku.id', $sku_id);

        if(!is_null($fields))
        {
            $builder->select($fields);
        }
        else
        {
            $builder->select('sku.*')
                  ->select('item.name as item_name');
        }
        $query = $builder->get(self::$table_name);
        return self::row($query);
    }

}
