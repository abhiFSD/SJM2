<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stocktakenew extends MY_Controller 
{
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
        $loggedInUser = $this->session->userdata('user_id');
        $errorMsg = "";
        // save the data
        if($this->input->post('save') == "Submit") 
        {
            $count = $this->input->post('count');
            $warehouseId = $this->input->post("warehouse");
            $itemId = $this->input->post('item_id');
            $stockCount = $this->input->post('stock_count');
            $date = $this->input->post('date');
            $amount = $this->input->post('amount');//new SOH
            $position = $this->input->post('position');
            $previous_position = $this->input->post('position_checker');
            $previous_soh = $this->input->post('product');

            // load local array for existing data,
            // this logic need to be re-structured to do a DB check if the number of products increase
            $fields = array (
                'id',
                'sku_id',
                'location_id'
            );
            $criteria = "location_id = ". $warehouseId;
            $existingData = $this->data->fetch('inventory_item',$fields, $criteria);

            foreach ($existingData as $existingDatum)
            {
                $inventoryIds[$existingDatum['sku_id']] = $existingDatum['id'];
            }
           
            // first insert new entry into stock adjustments
            // update the entry in invetory table
            foreach($amount as $i=>$new_stock_count)
            {
                // skipping if
                // 1 The new stock count is blank
                // 2 new and previous position has no change
                if (trim($new_stock_count) === "" && $position[$i] == $previous_position[$i]) continue;

                $inventoryId = null;

                if (isset($inventoryIds[$itemId[$i]])) {
                    $inventoryId = $inventoryIds[$itemId[$i]];
                }
                // insert or update the inventory tables
                if($inventoryId) {
                    // update 
                    $invData = [];
                    $invData["date_updated"] = $date;
                    $criteria = "id = '".$inventoryId."'";

                    if($new_stock_count !== "")
                        $invData["SOH"] = intval($new_stock_count);

                    $invData['position'] = $position[$i];

                    $this->data->update('inventory_item', $invData, $criteria);
                    
                    // create the activity log
                    $log ['action_details'] = "Stocktake: Updated Inventory Item (ID:". $inventoryId ." )";
                    $log ['module'] = "Stocktake";
                     
                    $this->datalog->add($log);
                } else {
                    // insert
                    $invData = array (
                        "sku_id"        => $itemId[$i],
                        "location_id"   => $warehouseId,
                        "SOH"           => intval($new_stock_count),
                        "date_created" => $date,
                        "date_updated" => date('Y-m-d H:i:s'),
                    );
                    $invData['position'] = $position[$i];
                    
                    try {
                        $inventoryId = $this->data->insert('inventory_item', $invData);
                        // create the activity log
                        $log ['action_details'] = "Stocktake: Created Inventory Item (ID:". $inventoryId ." )";
                        $log ['module'] = "Stocktake";

                        $this->datalog->add($log);
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                }

                // save data to the log entry only if stock count is not empty
                //and if there is an actual stock changes
                if($new_stock_count !== "" && $previous_soh[$i] != $new_stock_count)
                {
                    $data = array(
                        "location_id"               => $warehouseId,
                        "counter_location_id"       => $warehouseId,
                        "item_id"                   => $itemId[$i],
                        "adjustment_amount"         => ($stockCount[$i]>0?'+'.$stockCount[$i]:$stockCount[$i]),
                        "SOH"                       => $new_stock_count,
                        "adjustment_type"           => "Stocktake",
                         "location_type"         => "inventory_location",
                        "adjustment_date"           => $date,
                        "date_created"              => date('Y-m-d H:i:s'),
                        "date_updated"              => date('Y-m-d H:i:s'),
                        "user_id"                   => $loggedInUser
                    );
                    // inserted
                    try {
                        $this->data->insert('stock_movement_log',$data);
                    } catch (Exception $e) {
                        $errorMsg = $e->getMessage();
                        break;
                    }
                }
            }
            redirect(site_url('stockmovement/listall/true'));
            exit;
        }
        // Load Action
        // get the warehouse details
        $fields = array (
            'id',
            'name'           
        );
        $warehouseData = $this->data->fetch('inventory_location',$fields, null, 'name asc');
        $data = array('warehouses' => $warehouseData, 'error' => $errorMsg);
        
        $this->load->view("templates/header.php");
        $this->load->view("stocktake/formnew", $data);        
        $this->load->view("templates/footer.php");
    }  
    
    /**
     * Function to load the products
     * 
     * @param int $id
     */
    public function products($id, $type = "")
    {
        $this->load->library('data');
        $options = new stdClass();
        $options->id = $id;
        $options->type = $type;
        
        if ($this->input->post('warehouse')) {
            if ($this->input->post('warehouse') == $id ) {
                $vardata['formdata'] = $this->input->post();
            }
        }

        $skuData = POW\InventoryItem::stocktake_form_products($options);
        $stockData = array();
        foreach ($skuData as $data) {
            $stockData[] = array (
                'POSITION' => $data['position'],
                'SKU_ID' => $data['id'],
                'SKU'    =>  $data['sku_value'],
                'SKU_Name'  =>  $data['name'],
                'Previous_Stock'    => $data['SOH']
            );
        }
        
        $vardata['products'] = $stockData;
        
        $pageData = $this->load->view("stocktake/productnew", $vardata, true);
        echo $pageData;
    }

    public function listall()
    {
        $this->load->library('data');
        $this->load->helper('utility');

        $warehouse_ids = $this->input->post('warehouse_ids');
        $warehouse_ids = $warehouse_ids ? $warehouse_ids : [6];

        $options = new stdClass();
        $options->warehouse_ids = $warehouse_ids;
        $options->group_by_warehouse = true;
        
        if ($this->input->post())
        {
            $options->item_category_type = $this->input->post('item_category_type');
        }
        else
        {
            $options->item_category_type = ['product'];
        }

        if ($this->input->post('action') == 'Download')
        {
            $this->load->dbutil();
            $this->load->helper('download');
            
            $delimiter = ",";
            $newline = "\r\n";
                
            $result = POW\InventoryItem::warehouse_soh($options, $return_query = true);
            $csvdata = $this->dbutil->csv_from_result($result, $delimiter, $newline);
            
            return force_download("StockOnHand.csv", $csvdata);
        } 
        elseif ($this->input->post())
        {
            $data['items'] = POW\InventoryItem::warehouse_soh($options);
            
            return $this->load->view('stocktake/sections/warehouse_inventory', $data);
        }
      
        $data['locations'] = POW\InventoryLocation::list_key_val('id', 'name', [['name', 'asc']]);
        $data['warehouse_ids'] = $warehouse_ids;
        $data['skipped'] = $this->session->flashdata('skipped');
        $data['items'] = POW\InventoryItem::warehouse_soh($options);
        $data['options'] = POW\Helpers\get_item_types();

        $this->view_data = $data;
        $this->view_data['body_id'] = 'stocktakenew-listall';
        $this->default_view("stocktake/listall");
    }

    public function totalinventory()
    {
        $options = new stdClass();
        $options->item_category_type = $this->input->post('item_category_type') ? $this->input->post('item_category_type') : null;
        $options->warehouse_ids = is_array($this->input->post('location')) ? $this->input->post('location') : null;

        if ($this->input->post('action') == 'Download') 
        {
            $this->load->dbutil();
            $this->load->helper('download');
            
            $delimiter = ",";
            $newline = "\r\n";
            
            $result = POW\InventoryItem::warehouse_soh($options, $return_query = true);
            $csvdata = $this->dbutil->csv_from_result($result, $delimiter, $newline);
            
            return force_download("totalinventory.csv", $csvdata);
        }
        else if ($this->input->post())
        {
            $data['items'] = POW\InventoryItem::warehouse_soh($options, $return_query = false);
        
            return $this->load->view("stocktake/sections/total_inventory_table", $data);
        }

        $data['locations'] = POW\InventoryLocation::list_key_val('id', 'name', [['name', 'asc']]);
        $data['items'] = POW\InventoryItem::warehouse_soh($options, $return_query = false);

        $this->load->view("templates/header.php");
        $this->load->view("stocktake/totalinventory", $data);
        $this->load->view("templates/footer.php");
    }
}