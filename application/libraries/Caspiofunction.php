<?php
/**
 * Library for commonly used Caspio Functions
 * 
 * @author Prasanth
 *
 */

class Caspiofunction
{
   
   // $CI->your_library->do_something();
    
    private $CI;
    
    private $capiotools;
     
    public function __construct()
    {
        $this->CI =   & get_instance();
        $this->CI->load->library('data');  
    }
    
    /**
     * Get the inventory locations
     * 
     * @param string $type
     * @return array 
     */
    public function getInventoryLocations($type = "")
    {
   
        $fields = array (
            'id',
         //   'type',
            'name'
        );
        
        $criteria = "";
        if ($type != "") {
        //    $criteria = "type = '". $type . "'";
        }
        
        $locations = $this->CI->data->fetch('inventory_location',$fields, $criteria);
        
        return $locations;
    }
    
    /**
     * get the items and amount based on the location
     * @param unknown $locationId
     */
    public function getItemsFromLocation($locationId = "")
    {
        $fields = array (
            'sku_id',
            'sku',
            'name',
            'SOH'
        );
        
        $criteria = "";
        if($locationId != "") {
            $criteria = "location_id = ". $locationId; 
        }
        
        $skuData = $this->CI->data->fetch('vw_inventory_item', $fields, $criteria, true);
        
        return $skuData;
    }
    
    /**
     * get the inventory Items based on location and sku
     * @param string $locationId
     * @param string $skuId
     * @return array
     */
    public function getInventoryItems($locationId = "", $sku = "")
    {
        $fields = array (
            'id',
            'sku_id',
            'SOH'
        );
        
        $criteriaStr = "";
        if ($locationId != "") {
            $criteria[] = "location_id = ". $locationId;
        }
        
        if ($sku != "") {
            $criteria[] = "sku_id = '". $sku . "'";
        }
        
        if (count($criteria) > 0) {
            $criteriaStr = implode(" and ", $criteria);
        }
       
        $skuData = $this->CI->data->fetch('inventory_item', $fields, $criteriaStr);
        
        return $skuData;
    }
    
    
    
    
    
}