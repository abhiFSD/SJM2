<?php

namespace POW;

class KioskDeployment extends BaseModel
{
    protected static $table_name = 'kiosk_deployment';

    protected static $fillable = [
        'id',
        'machine_id',
        'location_id',
        'license_agreement_id',
        'installed_date',
        'uninstalled_date',
        'status',
        'date_created',
        'date_updated',
        'photo',
    ];

    protected static $associations = [
        'kiosk_location' => ['func' => 'POW\KioskLocation::with_id', 'args' => [['attribute', 'location_id']]],
        'kiosk' => ['func' => 'POW\Kiosk::with_id', 'args' => [['attribute', 'machine_id']]],
        'license_agreement' => ['func' => 'POW\LicenseAgreement::with_id', 'args' => [['attribute', 'license_agreement_id']]],
    ];

    public static function first_installed_machine($machine_id)
    {
        $query = self::getdb()
            ->where('machine_id', $machine_id)
            ->where_in('status', ['Installed', 'Install Scheduled'])
            ->get(self::$table_name);

        return self::row($query);
    }

    public static function get_all($statuses = null)
    {
        if ($statuses)
        {
            self::getdb()->where_in('status', is_array($statuses) ? $statuses : [$statuses]);
        }

        $query = self::getdb()
            ->get(self::$table_name);

        return self::listify($query);
    }

    public static function is_machine_deployed($machine_id, $deployment_id = null)
    {
        if ($deployment_id)
        {
            self::getdb()->where('id <>', $deployment_id);
        }

        $query = self::getdb()
            ->where('machine_id', $machine_id)
            ->where('status', 'Installed')
            ->get(self::$table_name);

        return $query && $query->num_rows() ? true : false;
    }

}
