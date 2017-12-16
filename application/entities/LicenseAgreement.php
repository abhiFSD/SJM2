<?php

namespace POW;

class LicenseAgreement extends BaseModel
{
    protected static $table_name = 'license_agreement';

    protected static $fillable = [
        'id',
        'name',
        'day_due',
        'start_date',
        'end_date',
        'fixed_fee_exGST',
        'commission_1_rate',
        'commission_1_threshold',
        'commission_2_rate',
        'commission_2_threshold',
        'status',
        'licensor_id',
    ];

    public static function get_active_agreements($licensor_id)
    {
        $query = self::getdb()
            ->where('licensor_id', $licensor_id)
            ->where('status', 'Active')
            ->get(self::$table_name);

        return self::listify($query);
    }
}

