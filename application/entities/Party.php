<?php

namespace POW;

class Party extends BaseModel
{
    protected static $table_name = 'party';

    protected static $fillable = [
        'id',
        'form',
        'org_name',
        'first_name',
        'last_name',
        'display_name',
        'abn',
        'address_line_1',
        'address_line_2',
        'suburb',
        'state',
        'postcode',
        'country',
        'email',
        'land_line',
        'mobile',
        'dob',
        'gender',
        'date_created',
        'date_updated',
        'party_parent_id',
        'status',
    ];

    protected static $decoration = [
        'user_id',
        'role_id',
        'role_name',
    ];

    public static function employee_with_id($id)
    {
        $query = self::getdb()
            ->select('party.*')
            ->select('IFNULL(user.id, 0) user_id', false)
            ->select('role.id role_id')
            ->join('party_type_allocation', 'party_type_allocation.party_id = party.id')
            ->join('role', 'party_type_allocation.user_role_id = role.id')
            ->join('user', 'user.party_id = party.id', 'left')
            ->where('party_type_allocation.party_type_id', 6)
            // ->where('party.status', 'Active')
            ->where('party.id', $id)
            ->get('party');

        $party = new Party();
        $party->assign_row($query && $query->num_rows() ? $query->row() : null);

        return $party;
    }

    protected static function get_with_party_type_id($party_type_id)
    {
        $query = self::getdb()
            ->select('party.*')
            ->select('IFNULL(user.id, 0) user_id', false)
            ->select('role.name role_name')
            ->join('party_type_allocation', 'party_type_allocation.party_id = party.id')
            ->join('role', 'party_type_allocation.user_role_id = role.id')
            ->join('user', 'user.party_id = party.id', 'left')
            ->where('party_type_allocation.party_type_id', $party_type_id)
            // ->where('party.status', 'Active')
            ->get('party');
            
        return self::listify($query);
    }

    public static function employee_list()
    {
        return self::get_with_party_type_id(6);
    }

    public static function freight_providers()
    {
        return self::get_with_party_type_id(11);
    }

    public static function staff()
    {
        return self::get_with_party_type_id(10);
    }
}
