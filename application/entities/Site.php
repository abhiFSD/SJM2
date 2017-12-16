<?php

namespace POW;

class Site extends BaseModel
{
    protected static $table_name = 'site';

    protected static $fillable = [
        'id',
        'name',
        'address',
        'city',
        'state',
        'country',
        'postcode',
        'licensor_id',
        'category',
        'site_contact_id',
        'days_per_week',
        'status',
        'date_created',
        'date_updated',
        'security_phone_number',
        'concierge_phone',
    ];

    protected static $associations = [
        'licensor' => ['func' => 'POW\PartyTypeAllocation::with_id', 'args' => [['attribute', 'licensor_id']]],
    ];

    public static function get_all()
    {
        $query = self::getdb()
            ->order_by('state', 'asc')
            ->order_by('name', 'asc')
            ->get(self::$table_name);

        return self::listify($query);
    }

    public static function with_licensor_id($licensor_id)
    {
        $query = self::getdb()
            ->where('licensor_id', $licensor_id)
            ->where('status', 'Active')
            ->order_by('state', 'asc')
            ->order_by('name', 'asc')
            ->get(self::$table_name);

        return self::listify($query);
    }

    public static function in_state($states = [])
    {
        $db = self::getdb();

        if (!empty($states))
        {
            $db->where_in('state', $states);
        }

        $query = $db
            ->where('status', 'Active')
            ->order_by('name', 'asc')
            ->get(self::$table_name);

        return $query && $query->num_rows() ? $query->result() : [];
    }

    public static function get_states()
    {
        $query = self::getdb()
            ->distinct()
            ->select('state')
            ->order_by('state', 'asc')
            ->get(self::$table_name);

        return self::column($query, 'state');
    }

}
