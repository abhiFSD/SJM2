<?php

namespace POW;

class KioskModel extends BaseModel
{
    protected static $table_name = 'kiosk_model';
    
    protected static $fillable = [
        'id',
        'name',
        'make',
        'status',
    ];

    protected static $associations = [
    ];

    //==================================================
    // Static Methods
    //==================================================

    public static function get_all()
    {
        $query = self::getdb()
            ->where('status', 'Active')
            ->get(self::$table_name);

        return self::listify($query);
    }

    //==================================================
    // Helper Methods
    //==================================================

}
