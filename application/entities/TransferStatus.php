<?php

namespace POW;

class TransferStatus extends BaseModel
{
    protected static $table_name = 'transfer_status';
    
    protected static $fillable = [
        'id',
        'date_created',
        'status',
        'transfer_id',
        'party_role_allocation_id',
        'tracking_number',
        'est_pick_up',
        'actual_pick_up',
        'est_delivery',
        'actual_delivery',
        'location_type',
        'location_id',
        'user_id',
        'note',
        'handover',
        'tracking_link',
    ];

    protected static $time_attributes = ['date_created', 'handover'];

    protected static $associations = [
        'user' => ['func' => 'POW\User::with_id', 'args' => [['attribute', 'user_id']]],
    ];

    //==================================================
    // Static Methods
    //==================================================

    public static function with_transfer_id($transfer_id, $sort = 'asc')
    {
        $query = self::getdb()
            ->where('transfer_id', $transfer_id)
            ->order_by('id', $sort)
            ->get(self::$table_name);

        return self::listify($query);
    }
    
    public static function open_with_kiosk_id($kiosk_id)
    {
        $query = self::getdb()
            ->select(self::$table_name.'.*')
            ->join('transfer', self::$table_name.'.transfer_id = transfer.id')
            ->where("((transfer.location_to_type = 'kiosk' AND transfer.location_to_id = $kiosk_id) OR (transfer.location_from_type = 'kiosk' AND transfer.location_from_id = $kiosk_id))")
            ->where('transfer.status <>', 'complete')
            ->where(self::$table_name.'.status <>', 'filled')
            ->order_by('transfer.id', 'asc')
            ->order_by(self::$table_name.'.id', 'asc')
            ->get(self::$table_name);

        return self::listify($query);
    }

    public static function first_with_transfer_id($transfer_id)
    {
        $query = self::getdb()
            ->where('transfer_id', $transfer_id)
            ->order_by('id', 'asc')
            ->get(self::$table_name);

        return self::row($query);
    }
    
    public static function last_with_transfer_id($transfer_id)
    {
        $query = self::getdb()
            ->where('transfer_id', $transfer_id)
            ->order_by('id', 'desc')
            ->get(self::$table_name);

        return self::row($query);
    }

    //==================================================
    // Helper Methods
    //==================================================

    public function get_object()
    {
        if (!in_array('object', $this->_associations))
        {
            $type = strtolower($this->get_location_type());
            $id = $this->get_location_id();
            $object = null;

            if (in_array($type, ['staff', 'freight provider']))
            {
                $object = Party::with_id($id);
            }
            elseif ('kiosk' == $type)
            {
                $object = Kiosk::with_id($id, $installed_only = true);
            }
            elseif ('inventory_location' == $type)
            {
                $object = InventoryLocation::with_id($id);
            }

            $this->_associations['object'] = $object;
        }

        return $this->_associations['object'];
    }
    
}
