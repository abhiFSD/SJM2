<?php

namespace POW;

class Kiosk extends BaseModel
{
    protected static $table_name = 'kiosk';

    protected static $fillable = [
        'id',
        'number',
        'status',
        'party_type_allocation_id',
        'kiosk_model_id',
        'type',
        'warranty_parts',
        'warranty_labour',
        'date_purchased',
        'date_relinquished',
        'date_created',
        'date_updated',
    ];

    protected static $decoration = [
        'location_name',
        'location_id',
        'model_name',
    ];

    protected static $associations = [
        'kiosk_deployment' => ['func' => 'POW\KioskDeployment::first_installed_machine', 'args' => [['attribute', 'id']]],
    ];

    public static function get_all()
    {
        $query = self::getdb()
            ->distinct()
            ->select(self::$table_name.'.*')
            ->select('kiosk_location.name location_name')
            ->select('kiosk_location.id location_id')
            ->select('kiosk_model.name model_name')
            ->join('kiosk_deployment','kiosk_deployment.machine_id = kiosk.id', 'left')
            ->join('kiosk_location','kiosk_location.id = kiosk_deployment.location_id', 'left')
            ->join('kiosk_model', self::$table_name.'.kiosk_model_id = kiosk_model.id', 'left')
            ->get(self::$table_name);

        return self::listify($query);
    }

    public static function with_id($kiosk_id, $installed_only = false)
    {
        if ($installed_only)
        {
            self::getdb()->where('kiosk_deployment.status', 'Installed');
        }

        $query = self::getdb()
            ->distinct()
            ->select(self::$table_name.'.*')
            ->select('kiosk_location.name location_name')
            ->select('kiosk_location.id location_id')
            ->select('kiosk_model.name model_name')
            ->join('kiosk_deployment','kiosk_deployment.machine_id = kiosk.id', 'left')
            ->join('kiosk_location','kiosk_location.id = kiosk_deployment.location_id', 'left')
            ->join('kiosk_model', self::$table_name.'.kiosk_model_id = kiosk_model.id', 'left')
            ->where(self::$table_name.'.id', $kiosk_id)
            ->get(self::$table_name);

        return self::row($query);
    }

    // matches only deployed kiosks
    public static function with_number($number)
    {
        $query = self::getdb()
            ->select(self::$table_name.'.*')
            ->select('kiosk_location.name location_name')
            ->select('kiosk_location.id location_id')
            ->join('kiosk_deployment','kiosk_deployment.machine_id = kiosk.id')
            ->join('kiosk_location','kiosk_location.id = kiosk_deployment.location_id', 'left')
            ->where(self::$table_name.'.number', $number)
            ->where_in('kiosk_deployment.status', ['Installed'])
            ->get(self::$table_name);

        return self::row($query);
    }

    public static function available_machines($current_kiosk_id = null)
    {
        $deployments = self::getdb()
            ->select('machine_id')
            ->from('kiosk_deployment')
            ->where_in('status', ['Installed', 'Install Scheduled'])
            ->get_compiled_select();

        if ($current_kiosk_id)
        {
            self::getdb()->where('id <>', $current_kiosk_id);
        }

        $query = self::getdb()
            ->where('status', 'Active')
            ->where("id NOT IN ($deployments)", null, false)
            ->get(self::$table_name);

        return self::listify($query);
    }

    public static function get_installed($options = null)
    {
        $db = self::getdb();
        $site_id = $options && !empty($options->site_id) ? $options->site_id : null;
        $state = $options && !empty($options->state) ? $options->state : null;

        if ($site_id && is_array($site_id))
        {
            $db->where_in('kiosk_location.site_id', $site_id);
        }
        elseif ($site_id)
        {
            $db->where('kiosk_location.site_id', $site_id);
        }

        $db
            ->join('kiosk_deployment', 'kiosk_deployment.machine_id = kiosk.id')
            ->join('kiosk_location', 'kiosk_location.id = kiosk_deployment.location_id');

        if ($state)
        {
            $db
                ->join('site', 'kiosk_location.site_id = site.id')
                ->where('site.state', $state);
        }

        $query = $db
            ->distinct()
            ->select('kiosk.*')
            ->select('kiosk_location.name location_name')
            ->where('kiosk.status', 'Active')
            ->where_in('kiosk_deployment.status', ['Installed', 'Install Scheduled'])
            ->order_by('kiosk.number', 'asc')
            ->get(self::$table_name);

        return $query && $query->num_rows() ? $query->result() : [];
    }

    public static function new_number()
    {
        $query = self::getdb()
            ->select('number')
            ->order_by('number', 'desc')
            ->limit(1)
            ->get(self::$table_name);

        $last_number = $query && $query->num_rows() ? $query->row()->number : '';

        return sprintf('MPP%03d', 1 + +str_replace('MPP', '', $last_number));
    }

    public static function number_is_unique($number, $kiosk_id = 0)
    {
        if ($kiosk_id)
        {
            self::getdb()->where('id <>', $kiosk_id);
        }

        $query = self::getdb()
            ->where('number', $number)
            ->get(self::$table_name);

        return $query && $query->num_rows() ? false : true;
    }

}
