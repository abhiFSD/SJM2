<?php

class SiteModel extends CI_Model{

    const TABLE = 'site';

    /*
     * Function: get_site_categories()
     * Description: This gets all the distinct site categories from the db
     * 
     * 
     * @author: Avtar Gaur (developer.avtargaur@gmail.com)
     * Date: Jan 22, 2017
     */
    public function get_site_categories(){
        $this->db->distinct();
        $this->db->select('category');
        $this->db->order_by('category', 'asc');
        $results = $this->db->get(self::TABLE)->result_array();

        $result = array();
        foreach ($results as $key => $value) {
          $result[] = $value['category'];
        }
        return $result;
    }

    public function with_party_id($party_id)
    {
        $query = $this->db
            ->select('site.name')
            ->select('site.id')
            ->join('party_type_allocation', 'party_type_allocation.id = site.licensor_id')
            ->where('status', 'Active')
            ->where('party_type_allocation.party_id', $party_id)
            ->where('party_type_allocation.party_type_id', 2)
            ->order_by('site.name', 'asc')
            ->get('site');

        return $query && $query->num_rows() ? $query->result() : [];
    }

    /*
     * Function: get_all_sites()
     * Description: This gets all the sites
     */
    public function get_all_sites($status = "All")
    {
        if ($status != 'All') {
            $this->db->where('site.status', $status);
        }

        $query = $this->db
            ->select('site.*')
            ->select('party.display_name as licensor')
            ->join('party_type_allocation', 'site.licensor_id = party_type_allocation.id')
            ->join('party', 'party.id = party_type_allocation.party_id')
            ->get('site');

        return $query && $query->num_rows() ? $query->result() : [];
    }
    
    
}