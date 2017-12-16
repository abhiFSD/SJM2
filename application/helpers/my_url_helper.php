<?php if (!defined('BASEPATH')) die();

if (!function_exists('inventory_location_page_url'))
{
    function inventory_location_page_link($inventory_location, $string = '')
    {
        return '<a href="'.site_url('inventorylocation/manage/'.$inventory_location->id).'" class="">'.(empty($string) ? $inventory_location->name : $string).'</a>';
    }
}

if (!function_exists('kiosk_page_link'))
{
    function kiosk_page_link($kiosk, $string = 'long')
    {
        if ('short' == $string)
        {
            $string = $kiosk->number;
        }
        else if ('long' == $string)
        {
            $string = $kiosk->number.' - '.$kiosk->location_name;
        }

        return '<a href="'.site_url('kiosklocation/manage/'.$kiosk->location_id).'" class="">'.$string.'</a>';
    }
}

if (!function_exists('kiosk_map_link'))
{
    function kiosk_map_link($kiosk)
    {
        $location = $kiosk->kiosk_deployment->kiosk_location;

        $url = get_instance()->config->item('map_directions');
        $url .= '&destination='.$location->lat.','.$location->lng;
        
        return '<a href="'.$url.'" target="_blank" class="red-placemark red-placemark-normal"></a>';
    }
}

if (!function_exists('inventory_location_map_link'))
{
    function inventory_location_map_link($inventory_location)
    {
        $url = get_instance()->config->item('map_directions');
        $url .= '&destination='.urlencode($inventory_location->get_string());
        
        return '<a href="'.$url.'" target="_blank" class="red-placemark red-placemark-normal"></a>';
    }
}
