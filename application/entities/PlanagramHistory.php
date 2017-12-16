<?php

namespace POW;

/****
 This is not a real table. This is a convenience data mapper class
 ****/

class PlanagramHistory extends BaseModel
{
    public static function get_history($criteria = array())
    {
        $result = array_merge(
            self::build('offering_attribute_allocation', $criteria), 
            self::build('offering_change_log', $criteria)
        );

        usort($result, function($a, $b) {
            if ($a->kiosk_id != $b->kiosk_id)
            {
                return +$a->kiosk_id > +$b->kiosk_id ? 1 : -1;
            }
            if ($a->position != $b->position)
            {
                return +$a->position > +$b->position ? 1 : -1;
            }
            if ($a->status != $b->status)
            {
                return strcasecmp($a->status, $b->status);
            }
            if ($a->offering_attribute_id != $b->offering_attribute_id)
            {
                return +$a->offering_attribute_id > +$b->offering_attribute_id ? 1 : -1;
            }

            return 0;
        });

        return $result;
    }

    protected static function build($table_name, $criteria)
    {
        $builder = self::getdb()
            ->select('the_table.kiosk_id')
            ->select('the_table.position')
            ->select('the_table.offering_attribute_id')
            ->select('the_table.status')
            ->select('kiosk_location.name location')
            ->select('offering_attribute.name attribute_name')
            ->select('the_table.value')
            ->select('kiosk.number')
            ->select('sku.name sku_name')
            ->from("$table_name as the_table")
            ->join('offering_attribute', 'the_table.offering_attribute_id = offering_attribute.id')
            ->join('kiosk', 'the_table.kiosk_id = kiosk.id')
            ->join('kiosk_model', 'kiosk_model.id = kiosk.kiosk_model_id')
            ->join('kiosk_deployment', "kiosk_deployment.machine_id = kiosk.id AND kiosk_deployment.status = 'Installed'")
            ->join('kiosk_location', 'kiosk_location.id = kiosk_deployment.location_id')
            ->join('site', 'kiosk_location.site_id = site.id')
            ->join('sku', 'sku.id = the_table.value AND the_table.offering_attribute_id = 1', 'left');

        if ($table_name == 'offering_attribute_allocation')
        {
            $builder
                ->select('the_table.date_queued')
                ->select('the_table.date_applied')
                ->select('the_table.user_applied')
                ->select("'' date_unapplied");
        }
        else
        {
            $builder
                ->select("the_table.date_queued")
                ->select("the_table.date_applied")
                ->select('the_table.user_unapplied user_applied')
                ->select('the_table.date_unapplied');
        }

        if (!empty($criteria['site_category'])) 
        {
            $builder->where('site.category', $criteria['site_category']);
        }
        if (!empty($criteria['kiosk_model'])) 
        {
            $builder->where('kiosk_model.id', $criteria['kiosk_model']);
        }
        if (!empty($criteria['kiosk_name'])) 
        {
            $builder->where('kiosk.id', $criteria['kiosk_name']);
        }
        if (!empty($criteria['state'])) 
        {
            $builder->where('site.state', $criteria['state']);
        }
        if (!empty($criteria['position'])) 
        {
            $builder->where('the_table.position', $criteria['position']);
        }
        if (!empty($criteria['capacity'])) 
        {
            $builder->where('the_table.offering_attribute_id', 5);
            $builder->where('the_table.value', $criteria['par']);
        }
        if (!empty($criteria['par'])) 
        {
            $builder->where('the_table.offering_attribute_id', 6);
            $builder->where('the_table.value', $criteria['par']);
        }
        if (!empty($criteria['width'])) 
        {
            $builder->where('the_table.offering_attribute_id', 8);
            $builder->where('the_table.value', $criteria['width']);
        }
        if (!empty($criteria['min_price'])) 
        {
            $builder->where('the_table.offering_attribute_id', 2);
            $builder->where('the_table.value  >=', $criteria['min_price']);
        }
        if (!empty($criteria['max_price'])) 
        {
            $builder->where('the_table.offering_attribute_id', 2);
            $builder->where('the_table.value <=', $criteria['max_price']);
        }
        if (!empty($criteria['sku_id'])) 
        {
            $builder->where('the_table.offering_attribute_id', 1);
            $builder->where('the_table.value', $criteria['sku_id']);
        }
        if (!empty($criteria['status'])) 
        {
            $builder->where('the_table.status', $criteria['status']);
        }

        $query = $builder->get();

        return $query && $query->num_rows() ? $query->result() : [];
    }

}
