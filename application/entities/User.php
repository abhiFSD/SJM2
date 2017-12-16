<?php

namespace POW;

class User extends BaseModel
{
    protected static $table_name = 'user';

    protected static $fillable = [
        'id',
        'email_address',
        'active',
        'display_name',
        'role_id',
        'party_id',
        'inventory_location_id'
    ];

    protected static $hidden = [
        'password',
    ];

    protected static $associations = [
        'party' => ['func' => 'POW\Party::with_id', 'args' => [['attribute', 'party_id']]],
    ];

}
