<?php if (!defined('BASEPATH')) die();

function role_form_input($role_id, $data, $value = '', $extra = '', $hidden_input = false)
{
    $user_role_id = get_instance()->session->userdata('role_id');

    if ($user_role_id && +$user_role_id <= $role_id)
    {
        return form_input($data, $value, $extra);
    }
    else
    {
        $string = '<p>'.$value.'</p>';

        if ($hidden_input)
        {
            $string .= form_hidden($data, $value);
        }

        return $string;
    }
}

function role_form_dropdown($role_id, $name, $options, $selected = '', $extra = '')
{
    $user_role_id = get_instance()->session->userdata('role_id');

    if ($user_role_id && +$user_role_id <= $role_id)
    {
        return form_dropdown($name, $options, $selected, $extra);
    }
    else
    {
        return '<p>'.(array_key_exists($selected, $options) ? $options[$selected] : '').'</p>';
    }
}

function role_form_textarea($role_id, $data, $value = '', $extra = '')
{
    $user_role_id = get_instance()->session->userdata('role_id');

    if ($user_role_id && +$user_role_id <= $role_id)
    {
        return form_textarea($data, $value, $extra);
    }
    else
    {
        return '<p>'.$value.'</p>';
    }
}