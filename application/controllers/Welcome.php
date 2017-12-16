<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->acl->hasAccess();
	}
	
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
            
           // first insert new entry into stock adjustments
           // update the entry in invetory table
            for ($i = 0; $i < $count; $i++) {
                // save data
                $data = array(
                    "Inv_Locn_ID"               => $warehouseId,
                    "Counter_Inv_Locn_ID"       => $warehouseId,
                    "Inv_Item_ID"               => $itemId[$i],
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
            //  $criteria = "Inv_Locn_ID = ". $id . " and SKU = POW-J3000BE";
            $invData = $this->caspiotools->fetch('vw_Inventory_Items', $invFields, $criteria, true);
            //   print_r($invData);exit;
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
                'Inv_Item_ID'       => $invData['tbl_Inventory_Items_Inventory_Item_ID']
            );
        
        
        }
        
        
        //
        // get the warehouse details
       /* $fields = array (
            'SKU_ID',
            'tbl_SKUs_SKU_Name',
            'tbl_Inventory_Items_Inv_Item_SOH',  
            'tbl_Inventory_Items_Inventory_Item_ID'
        );
        
        $criteria = "Inv_Locn_ID = ". $id;
        
        $productData = $this->caspiotools->fetch('vw_Inventory_Items',$fields, $criteria, true);
        
        */
        $data = array('products' => $stockData);
        
        $pageData = $this->load->view("product", $data, true);
        echo $pageData;
    }
}
