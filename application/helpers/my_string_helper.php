<?php if (!defined('BASEPATH')) die();

if (!function_exists('plural'))
{
    function plural($count, $singular, $plural)
    {
        return $count == 1 ? $singular : $plural;
    }
}

if (!function_exists('number_noun_verb'))
{
    function number_noun_verb($number, $singular_noun, $plural_noun, $verb)
    {
        return $number.' '.plural($number, $singular_noun, $plural_noun).' '.$verb;
    }
}

if (!function_exists('number_noun'))
{
    function number_noun($number, $singular_noun, $plural_noun)
    {
        return $number.' '.plural($number, $singular_noun, $plural_noun);
    }
}
