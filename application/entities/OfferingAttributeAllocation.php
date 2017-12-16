<?php

namespace POW;

class OfferingAttributeAllocation extends BaseModel
{
    protected static $table_name = 'offering_attribute_allocation';

    protected static $fillable = [
        'id',
        'position',
        'offering_attribute_id',
        'value',
        'isOptional',
        'kiosk_id',
        'status',
        'queue_type',
        'date_queued',
        'date_applied',
        'date_unapplied',
        'user_queued',
        'user_applied',
        'user_unapplied',
        'commit_type',
    ];

    public static function getKioskPositions($kiosk_id)
    {
        $query = self::getdb()
            ->distinct()
            ->select('position')
            ->where('kiosk_id', $kiosk_id)
            ->order_by('position', 'asc')
            ->get('offering_attribute_allocation');

        return self::column($query, 'position');
    }

    public static function getItems($kiosk_id, $position, $status = null)
    {
        if (!empty($status))
        {
            self::getdb()->where('status', $status);
        }

        $query = self::getdb()
            ->where('kiosk_id', $kiosk_id)
            ->where('position', $position)
            ->get(self::$table_name);

        return self::listify($query);
    }

    public static function set_none($kiosk_id, $position)
    {
        return self::getdb()
            ->set('queue_type', 'none')
            ->set('date_applied', date('Y-m-d H:i:s'))
            ->set('user_applied', self::get_session()->userdata('user_id'))
            ->where('kiosk_id', $kiosk_id)
            ->where('position', $position)
            ->update(self::$table_name);
    }

    public static function get_queued_non_product_positions($kiosk_id)
    {
        $query = self::getdb()
            ->select('position')
            ->where('status', 'Queued')
            ->where('queue_type', 'update')
            ->where_not_in('offering_attribute_id', [4, 7, 8])
            ->where('kiosk_id', $kiosk_id)
            ->group_by('position')
            ->get(self::$table_name);

        return self::column($query, 'position');
    }

    public static function delete_position_attributes($kiosk_id, $position)
    {
        return self::getdb()
            ->where('kiosk_id', +$kiosk_id)
            ->where('position', +$position)
            ->where('queue_type', 'delete')
            ->delete(self::$table_name);
    }

    public static function with_offering_attribute_id($kiosk_id, $position, $attribute_id, $status = '')
    {
        if ($status)
        {
            self::getdb()->where('status', $status);
        }
        
        $query = self::getdb()
            ->where('kiosk_id', $kiosk_id)
            ->where('position', $position)
            ->where('offering_attribute_id', $attribute_id)
            ->order_by('status', 'asc')
            ->get(self::$table_name);

        return self::listify($query);
    }

    protected static function get_affected_attributes($kiosk_id, $position, $attribute_ids)
    {
        if (!empty($attribute_ids))
        {
            self::getdb()->where_in('offering_attribute_id', array_keys($attribute_ids));
        }

        $query = self::getdb()
            ->where('kiosk_id', $kiosk_id)
            ->where('position', $position)
            ->where('status', 'Queued')
            ->get('offering_attribute_allocation');

        return $query && $query->num_rows() ? $query->result() : [];
    }

    public static function commit_changes_in_position($user_id, $kiosk_id, $position, $attribute_ids = null)
    {
        $kiosk_id = +$kiosk_id;
        $position = +$position;

        self::delete_position_attributes($kiosk_id, $position);

        $attributes = self::get_affected_attributes($kiosk_id, $position, $attribute_ids);
        $offering_attribute_ids = [];

        foreach ($attributes as $attribute)
        {
            $offering_attribute_ids[] = +$attribute->offering_attribute_id;
        }

        if (count($offering_attribute_ids))
        {
            $offering_attribute_ids_string = implode(',', $offering_attribute_ids);
            $current_date = date('Y-m-d H:i:s');

            // copy current active attributes that will be replaced
            self::copy_replaced_attributes($user_id, $kiosk_id, $position, $offering_attribute_ids_string);

            // delete current active attributes that will be replaced
            self::delete_replaced_attributes($user_id, $kiosk_id, $position, $offering_attribute_ids_string);

            // promote queued attributes to active
            self::getdb()
                ->where('kiosk_id', $kiosk_id)
                ->where('position', $position)
                ->where('status', 'Queued')
                ->where('queue_type', 'update')
                ->where('(commit_type = 1 OR commit_type IS NULL)', null, false)
                ->set('queue_type', 'none')
                ->set('date_applied', $current_date)
                ->set('status', 'Active')
                ->set('user_applied', $user_id)
                ->update('offering_attribute_allocation');

            // swap active and queued attributes where queued commit_type = 0 temporary
            self::swap_temporary_attributes($user_id, $kiosk_id, $position, $offering_attribute_ids, $offering_attribute_ids_string);
        }

        return true;
    }

    protected static function copy_replaced_attributes($user_id, $kiosk_id, $position, $offering_attribute_ids_string)
    {
        $current_date = date('Y-m-d H:i:s');

        self::getdb()->query("
            INSERT INTO `offering_change_log` (
                `position`,
                `offering_attribute_id`,
                `value`,
                `kiosk_id`,
                `status`,
                `date_queued`,
                `user_queued`,
                `date_applied`,
                `user_applied`,
                `date_unapplied`,
                `user_unapplied`
            )
            SELECT 
                `position`,
                `offering_attribute_id`,
                `value`,
                `kiosk_id`,
                'Inactive',
                `date_queued`,
                `user_queued`,
                `date_applied`,
                `user_applied`,
                '$current_date',
                $user_id
            FROM `offering_attribute_allocation`
            WHERE 
                kiosk_id = $kiosk_id 
                AND position = $position 
                AND status = 'Active'
                -- POW-252 changes to on hand should be removed
                AND offering_attribute_id <> 4
                -- Make sure this is being replaced
                AND offering_attribute_id IN ($offering_attribute_ids_string)
        ");
    }

    protected static function delete_replaced_attributes($user_id, $kiosk_id, $position, $offering_attribute_ids_string)
    {
        self::getdb()->query("
            DELETE `oaa_active` 
            FROM `offering_attribute_allocation` AS `oaa_active`
            INNER JOIN `offering_attribute_allocation` AS `oaa_queued` 
                ON `oaa_active`.`status` = 'Active' 
                AND `oaa_queued`.`status` = 'Queued' 
                AND `oaa_queued`.`queue_type` = 'update'
                AND `oaa_queued`.`kiosk_id` = `oaa_active`.`kiosk_id`
                AND `oaa_queued`.`position` = `oaa_active`.`position`
                AND `oaa_queued`.`offering_attribute_id` = `oaa_active`.`offering_attribute_id`
            WHERE 
                `oaa_active`.`kiosk_id` = $kiosk_id 
                AND `oaa_active`.`position` = $position 
                AND `oaa_active`.`offering_attribute_id` IN ($offering_attribute_ids_string)
                AND (`oaa_queued`.`commit_type` = 1 OR `oaa_queued`.`commit_type` IS NULL)
        ");
    }

    protected static function swap_temporary_attributes($user_id, $kiosk_id, $position, $offering_attribute_ids, $offering_attribute_ids_string)
    {
        $current_date = date('Y-m-d H:i:s');

        // demote current active attributes to queued
        self::getdb()->query("
            UPDATE `offering_attribute_allocation` AS `oaa_active`
            INNER JOIN `offering_attribute_allocation` AS `oaa_queued`
                ON `oaa_active`.`status` = 'Active' 
                AND `oaa_queued`.`status` = 'Queued' 
                AND `oaa_queued`.`queue_type` = 'update'
                AND `oaa_queued`.`kiosk_id` = `oaa_active`.`kiosk_id`
                AND `oaa_queued`.`position` = `oaa_active`.`position`
                AND `oaa_queued`.`offering_attribute_id` = `oaa_active`.`offering_attribute_id`
            SET
                `oaa_active`.`status` = 'Queued',
                `oaa_active`.`queue_type` = 'update',
                `oaa_active`.`date_queued` = '$current_date',
                `oaa_active`.`user_queued` = $user_id,
                `oaa_active`.`date_unapplied` = '$current_date',
                `oaa_active`.`user_unapplied` = $user_id
            WHERE
                `oaa_active`.`kiosk_id` = $kiosk_id 
                AND `oaa_active`.`position` = $position 
                AND `oaa_active`.`offering_attribute_id` IN ($offering_attribute_ids_string)
                AND `oaa_queued`.`commit_type` = 0
        ");

        // promote temporary queued attributes to active
        self::getdb()
            ->set('status', 'Active')
            ->set('queue_type', 'none')
            ->set('date_applied', $current_date)
            ->set('user_applied', $user_id)
            ->where('kiosk_id', $kiosk_id)
            ->where('position', $position)
            ->where_in('offering_attribute_id', $offering_attribute_ids)
            ->where('commit_type', 0)
            ->update(self::$table_name);
    }

    public static function positions_with_queued_sku_available_soh($kiosk_id, $warehouse_id)
    {
        $options = new \stdClass();
        $options->warehouse_id = $warehouse_id;
        $options->return_warehouse_builder = true;
        $options->available_only = true;
        $options->order_by = [['available_soh', 'desc']];

        $builder = InventoryItem::warehouse_soh($options);

        $inventory_items = $builder->get_compiled_select();

        $query = $builder
            ->select('offering_attribute_allocation.position')
            ->where('status', 'Queued')
            ->where('queue_type', 'update')
            ->where('offering_attribute_id', 1)
            ->where('kiosk_id', $kiosk_id)
            ->join("($inventory_items) as inventory_items", 'value = inventory_items.skuid')
            ->get('offering_attribute_allocation');

        return self::column($query, 'position');
    }

}
