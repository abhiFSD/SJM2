<?php
/**
 * Library for commonly used Caspio Functions
 * 
 * @author Prasanth
 *
 */

class Location
{
   
   // $CI->your_library->do_something();
    
    private $CI;
    
    
     
    public function __construct()
    {
        $this->CI =   & get_instance();
      
    }
    
    /**
     * Get the inventory locations
     * 
     * @param string $type
     * @return array 
     */
    public function get($type = "")
    {
   
        $fields = array (
            'Inv_Locn_ID',
            'Inv_Locn_Type',
            'Inv_Locn_Name'
        );
        
        $criteria = "";
        if ($type != "") {
            $criteria = "Inv_Locn_Type = '". $type . "'";
        }
        
        $locations = $this->CI->caspiotools->fetch('tbl_Inventory_Locns',$fields, $criteria);
        
        return $locations;
    }
    
    /**
     * get the items and amount based on the location
     * @param unknown $locationId
     */
    public function getItemsFromLocation($locationId = "")
    {
        $fields = array (
            'SKU_ID',
            'SKU',
            'tbl_SKUs_SKU_Name',
            'tbl_Inventory_Items_Inv_Item_SOH'
        );
        
        $criteria = "";
        if($locationId != "") {
            $criteria = "Inv_Locn_ID = ". $locationId; 
        }
        
        $skuData = $this->CI->caspiotools->fetch('vw_Inventory_Items', $fields, $criteria, true);
        
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
            'Inventory_Item_ID',
            'SKU_ID',
            'Inv_Item_SOH'
        );
        
        $criteriaStr = "";
        if ($locationId != "") {
            $criteria[] = "Inv_Locn_ID = ". $locationId;
        }
        
        if ($sku != "") {
            $criteria[] = "SKU_ID = '". $sku . "'";
        }
        
        if (count($criteria) > 0) {
            $criteriaStr = implode(" and ", $criteria);
        }
       
        $skuData = $this->CI->caspiotools->fetch('tbl_Inventory_Items', $fields, $criteriaStr);
        
        return $skuData;
    }
    
}