<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller class for handling the initial request
 * 
 * @author Prasanth
 */
class Index extends MY_Controller
{
    
    /**
     * Form for selecting the warehouse
     */
    public function index()
    {

       // $this->load->library('caspiotools');
        
        $errorMsg = "";
        // save the data
        if($this->input->post('save') == "Save Data") {
        
            $count = $this->input->post('count');
            $warehouseId = $this->input->post("warehouse");
            $itemId = $this->input->post('item_id');
            $stockCount = $this->input->post('stock_count');
            
           // first insert new entry into stock adjustments
           // update the entry in invetory table
            for ($i = 0; $i < $count; $i++) {
                // save data
                $data = array(
                    "Sending_Warehouse_ID"      => $warehouseId,
                    "Receiving_Warehouse_ID"    => $warehouseId,
                    "Inventory_Item_ID"         => $itemId[$i],
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
                
                // update the value
                $data = array (
                                    'Inv_Item_SOH' => $stockCount[$i]
                                    
                                );
                
                try {
                    $this->caspiotools->update('tbl_Inventory_Items',$data,'Inventory_Item_ID = '. $itemId[$i]);
                } catch (Exception $e) {
                    $errorMsg = $e->getMessage();
                    break;
                }
            }
        
        
        }
        
        // 
        // get the warehouse details
        $fields = array (
            'Warehouse_ID',
            'Warehouse_Name'           
        );
        $warehouseData = $this->caspiotools->fetch('tbl_Warehouses',$fields);
        $data = array('warehouses' => $warehouseData, 'error' => $errorMsg);
        
        
        $this->load->view("templates/header.php");
        $this->load->view("form", $data);        
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
        
       
        
        //
        // get the warehouse details
        $fields = array (
            'SKU_ID',
            'tbl_SKUs_SKU_Name',
            'tbl_Inventory_Items_Inv_Item_SOH',  
            'tbl_Inventory_Items_Inventory_Item_ID'
        );
        
        $criteria = "Inv_Locn_ID = ". $id;
        
        $productData = $this->caspiotools->fetch('vw_Inventory_Items',$fields, $criteria, true);
        
        
        $data = array('products' => $productData);
        
        $pageData = $this->load->view("product", $data, true);
        echo $pageData;
    }
}