<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stocktake extends MY_Controller {

	 /**
     * Form for selecting the warehouse
     */
    public function index()
    {
        
        $this->load->library('caspiotools');
        
        $errorMsg = "";
        // save the data
        if($this->input->post('save') == "Save Data") {
        
            $count = $this->input->post('count');
            $warehouseId = $this->input->post("warehouse");
            $itemId = $this->input->post('item_id');
            $stockCount = $this->input->post('stock_count');
            
            // load local array for existing data,
            // this logic need to be re-structured to do a DB check if the number of products increase
            $fields = array (
                'Inventory_Item_ID',
                'SKU_ID',
                'Inv_Locn_ID'
            );
            $existingData = $this->caspiotools->fetch('tbl_Inventory_Items',$fields);
            
            $inventoryId = null;
            // first insert new entry into stock adjustments
            // update the entry in invetory table
            for ($i = 0; $i < $count; $i++) {
                foreach ($existingData as $existingDatum)
                {
                    if ($existingDatum['Inv_Locn_ID'] == $warehouseId && $existingDatum['SKU_ID'] == $itemId[$i]) {
                        $inventoryId = $existingDatum['Inventory_Item_ID'];
                        break;
                    }
                }
                 
                // insert or update the inventory tables
                if($inventoryId) {
                    $criteria = "Inventory_Item_ID = '".$inventoryId."'";
                
                    $invData = array (
                        "Inv_Item_SOH"  => $stockCount[$i]
                    );
                
                    $this->caspiotools->update('tbl_Inventory_Items', $invData, $criteria);
                } else {
                    $invData = array (
                        "SKU_ID"        => $itemId[$i],
                        "Inv_Locn_ID"   => $locationId,
                        "Inv_Item_SOH"  => $stockCount[$i],
                    );
                     
                    $this->caspiotools->insert('tbl_Inventory_Items', $invData);
                
                    $fields = array (
                        'Inventory_Item_ID',
                        'SKU_ID',
                        'Inv_Locn_ID'
                    );
                
                    // get the new inventory ID
                    $existing = $this->caspiotools->fetch('tbl_Inventory_Items',$fields, $criteria);
                     
                    $inventoryId = $existing[0]['Inventory_Item_ID'];
                }
                
                    // save data
                    $data = array(
                        "Inv_Locn_ID"               => $warehouseId,
                        "Counter_Inv_Locn_ID"       => $warehouseId,
                        "Inv_Item_ID"               => $inventoryId,
                        "Adjustment_Amount"         => $stockCount[$i],
                        "Adjustment_Type"           => "Stocktake"
                    );
                
                    // inserted
                    try {
                        $this->caspiotools->insert('tbl_Stock_Movement_Log',$data);
                    } catch (Exception $e) {
                        $errorMsg = $e->getMessage();
                        break;
                    }
            }
        }

        $fields = array (
            'Warehouse_ID',
            'Warehouse_Name'           
        );
        $warehouseData = $this->caspiotools->fetch('tbl_Warehouses',$fields);
        $data = array('warehouses' => $warehouseData, 'error' => $errorMsg);
        
        $this->load->view("templates/header.php");
        $this->load->view("stocktake/form", $data);        
        $this->load->view("templates/footer.php");
    }   
    
    /**
     * Function to load the products
     * 
     * @param int $id
     */
    public function products($id)
    {
        $this->load->library('caspiotools');
        
        $fields = array (
            'SKU_ID',
            'SKU',
            'SKU_Name'
        );
        $skuData = $this->caspiotools->fetch('tbl_SKUs', $fields);
         
        $productData = array();
        foreach ($skuData as $data) {
        
            $invFields = array ("SKU_ID", "tbl_Inventory_Items_Inv_Item_SOH", "tbl_Inventory_Items_Inventory_Item_ID");
            $criteria = "Inv_Locn_ID = ". $id . " and SKU = '". $data['SKU']. "'";
            $invData = $this->caspiotools->fetch('vw_Inventory_Items', $invFields, $criteria, true);
            if ($invData) {
                $prevStock = $invData['0']['tbl_Inventory_Items_Inv_Item_SOH'];
            }else {
                $prevStock = 0;
            }
        
            $stockData[] = array (
                'SKU_ID' => $data['SKU_ID'],
                'SKU'    =>  $data['SKU'],
                'SKU_Name'  =>  $data['SKU_Name'],
                'Previous_Stock'    => $prevStock,
                'Inv_Item_ID'       => $invData['0']['tbl_Inventory_Items_Inventory_Item_ID']
            );
        }
        
        $data = array('products' => $stockData);
        
        $pageData = $this->load->view("stocktake/product", $data, true);
        echo $pageData;
    }
}
