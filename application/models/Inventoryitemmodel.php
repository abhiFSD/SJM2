<?php

class InventoryitemModel extends CI_Model
{
    const TABLE = 'inventory_item';
    
    /**
     * Get the location based on the ID
     * 
     * @param integer $id
     * @return string
     */
    public function getById($id)
    {
        $query = $this->db
            ->where('id', $id)
            ->get(self::TABLE);

        return $query && $query->num_rows() ? $query->row() : false;
    }
    
    /**
     * get the inventory items based on the criteria
     * 
     * @param unknown $criteria
     */
    public function get($criteria = array())
    {
        $query = $this->db
            ->where($criteria)
            ->get(self::TABLE);

        return $query && $query->num_rows() ? $query->result() : [];
    }

}