<?php
namespace POW\Helpers;

function get_item_types()
{
    return [
        'product' => 'Product',
        'equipment' => 'Equipment',
        'part' => 'Part',
        'material' => 'Material',
    ];
}

function validate_date($date,$format='Y-m-d H:i:s')
{
    $d = \DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function format_date($date,$format='d-m-y H:i:s')
{
    return  date($format, strtotime($date));
}