<?php

class Stockmovementlogmodel extends CI_Model
{
    /**
     * Function to return the data baed on the parameters given from the form.
     *
     */
    public function getData($options)
    {
        // build the query
        $this->db
            ->select('sml.adjustment_date as DateTime')
            ->select('il.name From')
            ->select('k.number KFrom')
            ->select('sml.location_type')
            ->select('sml.item_id ProductID')
            ->select('sku.sku_value SKU')
            ->select('sku.name ProductName')
            ->select('sml.adjustment_amount Amount')
            ->select('sml.soh SOH')
            ->select('sml.adjustment_type MovementType')
            ->select('sml.description Description')
            ->select('user.display_name MovementBy')
            ->select("sml.date_created DateCreated")
            ->from('stock_movement_log sml')
            ->join('sku', ' sku.id = `sml`.`item_id`', 'left')
            ->join('user', ' user.id = sml.user_id', 'left')
            ->join('inventory_location as il', ' il.id = sml.location_id', 'left')
            ->join('kiosk as k', 'k.id = sml.location_id', 'left');

        if (!empty($options['datetime_3m']))
        {
            $escaped = $this->db->escape($options['datetime_3m']);
            $this->db->where("sml.adjustment_date > DATE_SUB(STR_TO_DATE($escaped, '%Y-%m-%d %H:%i:%s'), INTERVAL 3 MINUTE)", null, false);
        }
        if (!empty($options['user_id_filter']))
        {
            $this->db->where('sml.user_id', $options['user_id_filter']);
        }
        if (!empty($options['kiosk'])) {
            $this->db->where ('sml.location_id', $options['kiosk']);
        }
        if (!empty($options['location_type']) && empty($options['kiosks']))
        {
            $this->db->where('sml.location_type', $options['location_type']);
        }
        if (!empty($options['locationid'])) {
            $this->db->where ('il.id', $options['locationid']);
        }
        if (!empty($options['adjustment'])) {
            $this->db->where ('sml.adjustment_type', $options['adjustment']);
        }
        if (!empty($options['minamount'])) {
            $this->db->where ('sml.adjustment_amount <= '. $options['minamount']);
        }
        if (!empty($options['minamount'])) {
            $this->db->where ('sml.adjustment_amount >= '. $options['maxamount']);
        }
        if (!empty($options['mindate'])) {
            $this->db->where ('date(sml.adjustment_date) >= "'. $options['mindate']. '"');
        }
        if (!empty($options['maxdate'])) {
            $this->db->where ('date(sml.adjustment_date) <= "'. $options['maxdate'] . '"');
        }
        if (!empty($options['productname'])) {
            $this->db->where('sku.name', $options['productname'] );
        }
        if (!empty($options['description']) && strlen(trim($options['description']))) {
            $this->db->like('sml.description', trim($options['description']));
        }
        if (!empty($options['item_category_type']))
        {
            $this->db
                ->join('item', 'item.id = sku.product_id')
                ->where_in('item.product_type', $options['item_category_type']);
        }

        $q = clone $this->db;
        $count = $q->count_all_results();

        $this->db->order_by('DateTime desc, SKU asc, sml.id desc');

        $query = $this->db->get();

        return array($query, $count);
    }
    
}
