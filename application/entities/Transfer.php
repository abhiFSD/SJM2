<?php

namespace POW;

class Transfer extends BaseModel
{
    protected static $table_name = 'transfer';

    protected static $fillable = [
        'id',
        'submitter',
        'location_from_type',
        'location_to_type',
        'location_from_id',
        'location_to_id',
        'notes',
        'date_created',
        'date_updated',
        'status',
        'fill_level',
    ];

    protected static $associations = [
        'first_transfer_status' => ['func' => 'POW\TransferStatus::first_with_transfer_id', 'args' => [['attribute', 'id']]],
        'last_transfer_status' => ['func' => 'POW\TransferStatus::last_with_transfer_id', 'args' => [['attribute', 'id']]],
        'transfer_statuses' => ['func' => 'POW\TransferStatus::with_transfer_id', 'args' => [['attribute', 'id'], ['value', 'asc']]],
    ];

    public function delete()
    {
        if ($this->get_id())
        {
            self::getdb()
                ->where('transfer_id', $this->get_id())
                ->delete('transfer_item');

            self::getdb()
                ->where('transfer_id', $this->get_id())
                ->delete('transfer_status');

            return parent::delete();
        }
    }

    //==================================================
    // Static Methods
    //==================================================

    public static function delete_with_id($id)
    {
        self::getdb()
            ->where('transfer_id', $id)
            ->delete('transfer_item');

        self::getdb()
            ->where('transfer_id', $id)
            ->delete('transfer_status');

        self::getdb()
            ->where('id', $id)
            ->delete('transfer');

        return true;
    }
    
    public static function delete_most_recent_generated_pick($kiosk_id)
    {
        $query = self::getdb()
            ->where('location_to_id', $kiosk_id)
            ->where('location_to_type', 'kiosk')
            ->where('status', 'Pick Generated')
            ->get(self::$table_name);

        if ($query && $query->num_rows())
        {
            foreach ($query->result() as $transfer)
            {
                Transfer::delete_with_id($transfer->id);
            }
        }
    }

    public static function get($transfer_type, $criteria = null)
    {
        if (!empty($criteria))
        {
            $db = self::getdb();

            foreach ($criteria as $attribute => $value)
            {
                if (in_array($attribute, self::$fillable))
                {
                    if (empty($value)) continue;

                    if (is_array($value))
                    {
                        $db->where_in('transfer.'.$attribute, $value);
                    }
                    else
                    {
                        $db->where('transfer.'.$attribute, $value);
                    }
                }
            }

            if (!empty($criteria['from_state']))
            {
                self::build_state_query('from', $criteria['from_state']);
            }

            if (!empty($criteria['to_state']))
            {
                self::build_state_query('to', $criteria['to_state']);
            }

            if (!empty($criteria['location_type']))
            {
                $escaped = array_map(function($item) use ($db) { return $db->escape(strtolower($item)); }, $criteria['location_type']);
                $escaped = implode(',', $escaped);
                $escaped = "({$escaped})";

                self::getdb()
                    ->group_by('transfer.id')
                    ->join('transfer_status', 'transfer.id = transfer_status.id')
                    ->having("SUBSTRING_INDEX(GROUP_CONCAT(transfer_status.location_type ORDER BY transfer_status.id DESC), ',', 1) IN $escaped");
            }
        }

        if ($transfer_type == 'open')
        {
            self::getdb()->where('transfer.status <>', 'complete');
        }
        elseif ($transfer_type == 'closed')
        {
            self::getdb()->where('transfer.status', 'complete');
        }

        $query = self::getdb()
            ->select('transfer.*')
            ->get('transfer');

        return self::listify($query);
    }

    private static function build_state_query($direction, $criteria)
    {
        $db = self::getdb();
        $escaped = array_map(function($item) use ($db) { return $db->escape($item); }, $criteria);
        $escaped = implode(',', $escaped);
        $escaped = "({$escaped})";

        self::getdb()
            ->join("kiosk_deployment as {$direction}_kiosk_deployment", "transfer.location_{$direction}_type = 'kiosk' AND transfer.location_{$direction}_id = {$direction}_kiosk_deployment.machine_id AND {$direction}_kiosk_deployment.status = 'Installed'", 'left')
            ->join("kiosk_location as {$direction}_kiosk_location", "{$direction}_kiosk_deployment.location_id = {$direction}_kiosk_location.id", 'left')
            ->join("site as {$direction}_site", "{$direction}_kiosk_location.site_id = {$direction}_site.id", 'left')
            ->join("inventory_location as {$direction}_inventory_location", "transfer.location_{$direction}_type = 'inventory_location' AND transfer.location_{$direction}_id = {$direction}_inventory_location.id AND {$direction}_inventory_location.active = 1", 'left')
            ->where("(
                    (transfer.location_{$direction}_type = 'kiosk' AND {$direction}_site.state IN {$escaped}) OR 
                    (transfer.location_{$direction}_type = 'inventory_location' AND {$direction}_inventory_location.state IN {$escaped})
                )", null, false);
    }

    //==================================================
    // Helper Methods
    //==================================================
    
    public function get_location_from()
    {
        return $this->get_object('location_from', $this->get_location_from_type(), $this->get_location_from_id());
    }

    public function get_location_to()
    {
        return $this->get_object('location_to', $this->get_location_to_type(), $this->get_location_to_id());
    }

    private function get_object($key, $type, $id)
    {
        if (!in_array($key, $this->_associations))
        {
            if ('inventory_location' == $type)
            {
                $object = InventoryLocation::with_id($id);
            }
            elseif ('kiosk' == $type)
            {
                $object = Kiosk::with_id($id, $installed_only = true);
            }

            $this->_associations[$key] = $object;
        }

        return $this->_associations[$key];
    }

    public function has_transfer_items()
    {
        $query = self::getdb()
            ->where('transfer_id', $this->get_id())
            ->get('transfer_item');

        return $query && $query->num_rows() ? true : false;
    }

    public function has_status($status)
    {
        $query = self::getdb()
            ->where('transfer_id', $this->get_id())
            ->like('status', $status)
            ->get('transfer_status');

        return $query && $query->num_rows() ? true : false;
    }

}
