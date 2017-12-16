<?php

namespace POW;

class StockMovementLog extends BaseModel
{
    protected static $table_name = 'stock_movement_log';
    
    protected static $fillable = [
        'id',
        'location_type',
        'location_id',
        'counter_location_id',
        'item_id',
        'adjustment_amount',
        'SOH',
        'adjustment_type',
        'adjustment_date',
        'user_id',
        'date_created',
        'date_updated',
        'description',
    ];

    protected static $time_attributes = ['date_created', 'date_updated'];

    protected static $associations = [
    ];

    //==================================================
    // Static Methods
    //==================================================

    //==================================================
    // Helper Methods
    //==================================================

}
