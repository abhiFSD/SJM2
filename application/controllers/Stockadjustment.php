<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stockadjustment extends MY_Controller {

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
            $locationId = $this->input->post("location");
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
            
            switch ($this->input->post("adjustment")) {
                case "1":
                    $counter = $this->input->post('ordernumber');
                    $type = "Stock Order";
                    break;
                case "2":
                    $counter = $this->input->post('received_from');
                    $type = "Inbound Interstate Transfer";
                    break;
                case "3":
                    $counter = $this->input->post('from_machine');
                    $type = "From Machine";
                    break;
                case "4":
                    $counter = 1; //$this->input->post('pick_list');
                    $type = "Pick List";
                    break;
                case "5":
                    $counter = $this->input->post('sent_to');
                    $type = "Outbound Interstate Transfer";
                    break;
                case "6":
                    $counter = $this->input->post('pick_adjustment');
                    $type = "Pick Adjustment";
                    break;
                case "7":
                    $counter = $this->input->post('other');
                    $type = "Other";
                    break;
            
            
            }

            // first insert new entry into stock adjustments
            // update the entry in invetory table
            for ($i = 0; $i < $count; $i++) {
                
                foreach ($existingData as $existingDatum)
                {
                    
                    if ($existingDatum['Inv_Locn_ID'] == $locationId && $existingDatum['SKU_ID'] == $itemId[$i]) {
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
                
                
                
                
                // save log data
                $data = array(
                    "Inv_Locn_ID"               => $locationId,
                    "Inv_Item_ID"               => $inventoryId,
                    "Adjustment_Amount"         => $stockCount[$i],
                    "Adjustment_Type"           => $type,
                    "Counter_Inv_Locn_ID"       => $counter,
                   
                );
                
                // inserted
                try {
                    $this->caspiotools->insert('tbl_Stock_Movement_Log',$data);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    break;
                }
                
            }
           
        }
        
        // 
        // get the warehouse details
        $fields = array (
            'Inv_Locn_ID',
            'Inv_Locn_Name'           
        );
        $locationData = $this->caspiotools->fetch('tbl_Inventory_Locns',$fields);
        $data = array('locations' => $locationData, 'error' => $errorMsg);
        
        
        $this->load->view("templates/header.php");
        $this->load->view("stockadjustment/index", $data);        
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
            'SKU',
            'SKU_Name'
        );
        
      //  $criteria = "Inv_Locn_ID = ". $id;
        
        $skuData = $this->caspiotools->fetch('tbl_SKUs', $fields);
       
        $productData = array();
        foreach ($skuData as $data) {
            
            $invFields = array ("SKU_ID", "tbl_Inventory_Items_Inv_Item_SOH");
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
                                    'Previous_Stock'    => $prevStock
                                );
            
          
        }
        
        $data = array('products' => $stockData);
        
        $pageData = $this->load->view("stockadjustment/product", $data, true);
        echo $pageData;
    }
}
