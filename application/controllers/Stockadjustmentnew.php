<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stockadjustmentnew extends MY_Controller {


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

        $this->load->model('DeploymentModel');
        $this->load->model('InventorylocationModel');
        
        $deployments = $this->DeploymentModel->getAll();
        
        $errorMsg = "";
        // save the data
        if($this->input->post('save') == "Submit") {
            $count = $this->input->post('count');
            $locationId = $this->input->post("location");

            $symbol = '+';
            switch ($this->input->post("adjustment")) 
            {
                case "1":
                    $counter = "Order Number: ";
                    $counter .= $this->input->post('ordernumber');
                    $type = "Stock Order";
                    break;

                case "2":
                    $counter = $this->input->post('stock_machine');
                    $type = "Over Pick";
                    break;

                case "3":
                    $counter = $this->input->post('damaged_stock_machine');
                    $type = "Damaged Stock From Machine";
                    break;

                case "4":
                    $counter = "";
                    $type = "Pick List Total";
                    $symbol = "-";
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
                    $counter = $this->input->post('other7');
                    $type = "Other - Add to Stock On Hand";
                    break;

                case "8":
                    $counter = $this->input->post('swapped_machine');
                    $type = "Swapped Out From Machine";
                    break;

                case "10":
                    $counter = "";
                    $type = "Interstate Transfer - Sent";
                    $symbol = "-";
                    break;

                case "11":
                    $counter = "";
                    $type = "Interstate Transfer - Received";
                    break;
            
                case '9':
                    $type = "Other - Subtract from Stock On Hand";
                    $counter = $this->input->post('other9');
                    $symbol = "-";
                    break;

                case '12':
                    $type = "Online Orders";
                    $counter = '';
                    $symbol = "-";
                    break;

                case '13':
                    $type = "Replacement";
                    $counter = '';
                    $symbol = "-";
                    break;

                case '14':
                    $type = "Sample / Gift";
                    $counter = $this->input->post('other14');
                    $symbol = "-";
                    break;
            }
            
            $config['upload_path']          = './uploads/';
            $config['allowed_types']        = 'csv';
            $config['max_size']             = 2048;
            
            $this->load->library('upload', $config);
            
            if ($this->upload->do_upload('upload_file')) 
            {
                $data =  $this->upload->data();
                
                list($itemId, $stockCount, $amount, $skipped) = $this->insertCSVData($data,$locationId, $symbol);

                $this->session->set_flashdata('skipped', $skipped);
                $count = count($stockCount);
            } 
            else 
            {
                $itemId = $this->input->post('item_id');
                $stockCount = $this->input->post('stock_count');
                $amount = $this->input->post('amount');
                $count = count($stockCount);
            }

            $date = $this->input->post('date');
            
            // load local array for existing data,
            // this logic need to be re-structured to do a DB check if the number of products increase
            $fields = array (
                'id',
                'sku_id',
                'location_id'
            );
            $criteria = "location_id = ". $locationId;
            $existingData = $this->data->fetch('inventory_item',$fields, $criteria);
            
            $inventoryIds = array();
            foreach ($existingData as $existingDatum)
            {
                $inventoryIds[$existingDatum['sku_id']] = $existingDatum['id'];
            }

            $inventoryId = null;
            
            // first insert new entry into stock adjustments
            // update the entry in inventory table
            
            for ($i = 0; $i < $count; $i++)
            {
                // skipping if the new stockcount and position is blank
                if ($stockCount[$i] == "") continue;

                $criteria = "";
                $inventoryId = "";
                $movements = false;

                if (isset($inventoryIds[$itemId[$i]])) {
                    $inventoryId = $inventoryIds[$itemId[$i]];
                }

                // insert only
                if (!$movements) 
                {
                    $invData = [];
                    // insert or update the inventory tables
                    if($inventoryId) 
                    {
                        $criteria = "id = '".$inventoryId."'";
                        $invData["SOH"] = $stockCount[$i];

                        $this->data->update('inventory_item', $invData, $criteria);

                        // create the activity log
                        $log ['action_details'] = "Stock Adjustment: Updated Inventory Item (ID:". $inventoryId ." )";
                        $log ['module'] = "StockAdjustment";

                        $this->datalog->add($log);
                    } 
                    else 
                    {

                        $invData = array (
                            "sku_id"        => $itemId[$i],
                            "location_id"   => $locationId,
                            "SOH"           => $stockCount[$i],
                            "date_created"  => date('Y-m-d H:i:s'),
                            "date_updated"  => date('Y-m-d H:i:s'),
                        );
                       
                        $inventoryId = $this->data->insert('inventory_item', $invData); 

                        // create the activity log
                        $log ['action_details'] = "Stock Adjustment: Created Inventory Item (ID:". $inventoryId ." )";
                        $log ['module'] = "Stock Adjustment";
                        $this->datalog->add($log);
                    }

                    // save log data
                    $data = array(
                        "location_id"       => $locationId,
                        "item_id"           => $itemId[$i],
                        "adjustment_amount" => ($symbol == '=' ? '' : $symbol) . $amount[$i],
                        "SOH"               => $stockCount[$i],
                        "adjustment_type"   => $type,
                        "adjustment_date"   => $date,
                        "description"       => $counter,
                        "date_created"      => date('Y-m-d H:i:s'),
                        "date_updated"      => date('Y-m-d H:i:s'),
                        "location_type"     => "inventory_location",
                        "user_id"           => $loggedInUser
                    );

                    // inserted
                    try
                    {
                        $this->data->insert('stock_movement_log', $data);
                    } catch (Exception $e)
                    {
                        echo $e->getMessage();
                        break;
                    }
                } 
            }

            redirect(site_url('stockmovement/listall/true'));
        }
        
        // get the warehouse details
        $locationData = $this->InventorylocationModel->findWarehouses();
        
        $machineData = $this->data->fetch('kiosk', array(
            'number'
        ));

        $data = array(
            'locations' => $locationData,
            'error' => $errorMsg,
            'machines' => $machineData,
            'deployments' => $deployments,
            'date' => date("Y-m-d") ,
            'time' => date("H:i:s") . ".000"
        );

        $this->load->view("templates/header.php");
        $this->load->view("stockadjustment/indexnew", $data);
        $this->load->view("templates/footer.php");
    }   
    
    private function insertCSVData($fileData, $location, $symbol)
    {
        $itemId = [];
        $stockCount = [];
        $amount = [];

        if (($handle = fopen($fileData['full_path'], "r")) !== FALSE) 
        {
            $skipped = array();

            // flush first line
            fgetcsv($handle, 1000, ",");

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
            {
                if ( $data[1] > 0) 
                {
                    $query = $this->db->get_where('sku', array('sku_value' => $data[0]));

                    if ($query->num_rows() > 0) 
                    {
                        $sku = $query->result();
                        
                        // get the product
                        $query = $this->db->get_where('inventory_item', array('sku_id' => $sku[0]->id, 'location_id' => $location));

                        $currentValue = $query->num_rows() ? $query->row()->SOH : 0;
                            
                        $newVal = 0;
                        if ($symbol == '+') {
                            $newVal = $currentValue+$data[1];
                        } 
                        else if ('=' == $symbol) {
                            $newVal = $data[1];
                        }
                        else {
                            $newVal = $currentValue-$data[1];
                        }

                        if ($newVal >= 0) {
                            $itemId[] = $sku[0]->id;
                                $stockCount[] = $newVal;
                                $amount[] = $data[1];
                        } else {
                            $skipped[] = array (
                                "value" => $data[0],
                                "reason" => "SOH will be negative"
                            ); 
                        }
                    } 
                
                } else {
                        $skipped[] = array (
                            "value" => $data[0],
                            "reason" => "Adjustment amount is zero"
                        );
                }
            }
        }  

        return array($itemId, $stockCount, $amount, $skipped);
    }

    public function products($id)
    {
        $this->load->library('data');
        $adjustment = $this->input->post('adjustment');
        $type = $this->input->post('type');

        if(is_null($type) && !is_array($type))
            $type = [];

        if ($id && $adjustment)
        {
            $vardata['formdata'] = $this->input->post();
        }

        $options = new stdClass();
        $options->id = $id;
        $options->type = implode(',',$type);
        $options->order_by = 'sku.sku_value';

        $skuData = POW\InventoryItem::stocktake_form_products($options);
        $stockData = array();

        foreach ($skuData as $data)
        {
            $stockData[] = array (
                'POSITION' => $data['position'],
                'SKU_ID' => $data['id'],
                'SKU'    =>  $data['sku_value'],
                'SKU_Name'  =>  $data['name'],
                'Previous_Stock'    => $data['SOH']
            );
        }

        $vardata['products'] = $stockData;

        $pageData = $this->load->view("stockadjustment/productnew", $vardata, true);
        echo $pageData;
    }
}