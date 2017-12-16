<?php

namespace POW;

class KioskLocation extends BaseModel
{
    protected static $table_name = 'kiosk_location';

    protected static $fillable = [
        'id',
        'name',
        'sales_multiplier',
        'site_id',
        'level',
        'location_within_site',
        'nearest_loading_dock_parking',
        'warehouse_id',
        'lat',
        'lng',
        'status',
        'photo',
        'date_created',
        'date_updated',
    ];

    protected static $decoration = [
        'sitename',
    ];

    protected static $associations = [
        'site' => ['func' => 'POW\Site::with_id', 'args' => [['attribute', 'site_id']]],
    ];

    public static function with_kiosk_id($kiosk_id)
    {
        $query = self::getdb()
            ->select('kiosk_location.*')
            ->join('kiosk_deployment', 'kiosk_location.id = kiosk_deployment.location_id')
            ->join('kiosk', 'kiosk_deployment.machine_id = kiosk.id')
            ->where('kiosk.id', $kiosk_id)
            ->where('kiosk_deployment.status', 'Installed')
            ->get('kiosk_location');

        $kioskLocation = new KioskLocation();
        $kioskLocation->assign_row($query && $query->num_rows() ? $query->row() : null);

        return $kioskLocation;
    }

    public static function of_licensor($licensor_id)
    {
        $query = self::getdb()
            ->select('kiosk_location.*')
            ->join('site', 'site.id = kiosk_location.site_id')
            ->where('kiosk_location.status', 'Active')
            ->where('site.licensor_id', $licensor_id)
            ->order_by('kiosk_location.name', 'asc')
            ->get('kiosk_location');

        return self::listify($query);
    }

    public static function with_status($status = '')
    {
        if ($status)
        {
            self::getdb()->where(self::$table_name.'.status', $status);
        }

        $query = self::getdb()
            ->select(self::$table_name.'.*')
            ->select('site.name sitename')
            ->join('site', self::$table_name.'.site_id = site.id')
            ->get(self::$table_name);

        return self::listify($query);
    }

}
