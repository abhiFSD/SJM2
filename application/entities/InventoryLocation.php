<?php

namespace POW;

class InventoryLocation extends BaseModel
{
    protected static $table_name = 'inventory_location';

    protected static $fillable = [
        'id',
        'name',
        'street_address_1',
        'street_address_2',
        'suburb',
        'post_code',
        'state',
        'country',
        'date_created',
        'date_updated',
        'active',
    ];

    //==================================================
    // Static Methods
    //==================================================

    public static function get_all($filterActive = false, $state = null)
    {
        // filter only the active ones!
        if ($filterActive) 
        {
            self::getdb()->where('active', 1);
        }

        if ($state) 
        {
            self::getdb()->where('state', $state);
        }
        
        $query = self::getdb()
            ->order_by('active', 'desc')
            ->order_by('name', 'asc')
            ->get(self::$table_name);

        return self::listify($query);
    }

    //==================================================
    // Helper Methods
    //==================================================

    public function get_string()
    {
        $string = $this->get_street_address_1().' '.$this->get_street_address_2();

        if ($this->get_suburb())
        {
            $string .= ', '.$this->get_suburb();
        }

        if ($this->get_state())
        {
            $string .= ' '.$this->get_state();
        }

        if ($this->get_post_code())
        {
            $string .= ' '.$this->get_post_code();
        }

        if ($this->get_country())
        {
            $string .= ' '.$this->get_country();
        }

        return str_replace('  ', ' ', trim($string));
    }
    
}
