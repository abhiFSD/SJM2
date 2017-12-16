<?php

namespace POW;

class OfferingAttribute extends BaseModel
{
    protected static $table_name = 'offering_attribute';

    protected static $fillable = [
        'id',
        'name',
        'isItem',
        'isPicked',
    ];

}
