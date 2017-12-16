<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('match_attribute'))
{
    function match_attribute($items, $position, $offering_attribute_id, $sku_id)
    {
        foreach ($items as $item)
        {
            if (+ $item->position === + $position && + $item->offering_attribute_id == $offering_attribute_id && $item->skuid == $sku_id)
            {
                return $item;
            }
        }

        return false;
    }
}
if ( ! function_exists('get_attribute_id_map'))
{
    function get_attribute_id_map()
    {
       return [
           2 => 'price',
           3 => 'price3',
           5 => 'capacity',
           6 => 'par',
           9 => 'coil',
           10 => 'pusher',
           11 => 'stabiliser',
           12 => 'platform'
       ];
    }
}