<?php

namespace POW;

class PartyTypeAllocation extends BaseModel
{
    protected static $table_name = 'party_type_allocation';

    protected static $fillable = [
        'id',
        'party_type_id',
        'party_id',
        'user_role_id',
        'active',
    ];

    protected static $decoration = [
        'org_name',
        'display_name',
    ];

    protected static $associations = [
        'party' => ['func' => 'POW\Party::with_id', 'args' => [['attribute', 'party_id']]],
    ];

    public static function get_licensors()
    {
        return self::with_party_type_id(2);
    }

    public static function with_party_type_id($party_type_id)
    {
        $query = self::getdb()
            ->select('party_type_allocation.*')
            ->select('party.org_name')
            ->select('party.display_name')
            ->join('party_type_allocation', 'party_type_allocation.party_id = party.id')
            ->where('party_type_allocation.party_type_id', $party_type_id)
            ->order_by('party.display_name', 'asc')
            ->get('party');

        return self::listify($query);
    }

}
