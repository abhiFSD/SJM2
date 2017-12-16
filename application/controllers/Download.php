<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Download extends MY_Controller {


	public function __construct()
    {
        parent::__construct();
    }


    public function execute()
    {

        $jobs = $this->db->get_where('download', array('completed' => 0));

        foreach ($jobs->result() as $job) {
            $result = $this->result(unserialize($job->params), $job->id);
            if ($result) {
                $update = array(
                    'completed' => 1,
                    'file'  => $result
                );

                $this->db->update('download', $update, array('id' => $job->id));
            }
        }
    }

    private function result($postData, $id)
    {
        $this->load->library('data');


        $this->db->select('date_format(sml.adjustment_date, "%d-%m-%Y %l:%i %p") as DateTime, il.name as From,  
						sku.sku_value as ProductID, sku.name as ProductName,sml.adjustment_amount as Amount, sml.soh as SOH, 
		                sml.adjustment_type as MovementType, sml.description as Description, user.display_name as MovementBy, 
		                date_format(sml.date_created, "%d-%m-%Y %l:%i %p") as DateCreated,');
        $this->db->from('stock_movement_log as sml');
        $this->db->join('inventory_location as il', ' il.id = sml.location_id', 'left');
        $this->db->join('inventory_item as item', 'item.id = sml.item_id', 'left');
        $this->db->join('sku', ' sku.id = item.sku_id', 'left');
        $this->db->join('user', ' user.id = sml.user_id', 'left');

        if (isset($postData['location']) && $postData['location'] != "") {
            $this->db->where ('il.id', $postData['location']);
        }

        if (isset($postData['adjustment']) && $postData['adjustment'] != "") {
            $this->db->where ('sml.adjustment_type', $postData['adjustment']);
        }

        if (isset($postData['minamount']) && $postData['minamount'] != "") {
            $this->db->where ('sml.adjustment_amount <= '. $postData['minamount']);
        }
        if (isset($postData['minamount']) && $postData['minamount'] != "") {
            $this->db->where ('sml.adjustment_amount >= '. $postData['maxamount']);
        }

        if (isset($postData['mindate']) && $postData['mindate'] != "") {
            $this->db->where ('sml.adjustment_date >= "'. $postData['mindate']. '"');
        }
        if (isset($postData['maxdate']) && $postData['maxdate'] != "") {
            $this->db->where ('sml.adjustment_date <= "'. $postData['maxdate'] . '"');
        }

        if (isset($postData['product']) && $postData['product'] != "") {
            $this->db->where('sku.name', $postData['product'] );
        }

        $this->db->order_by('sml.date_created', 'desc');

        $query = $this->db->get();

       // echo $this->db->last_query();
        $this->load->dbutil();


        $delimiter = ",";
        $newline = "\r\n";

        $csvdata = $this->dbutil->csv_from_result($query, $delimiter, $newline);


        $this->load->helper('file');

        $date = new DateTime();
        $uniqueId = $date->getTimestamp();
        $filename = 'stockmovement_'.$uniqueId. "_".$id.".csv";

        if ( !write_file(BASEPATH. '/../downloads/'. $filename, $csvdata)){
           return false;
        }

        return $filename;


    }

    public function listall()
    {
        $userId = $this->session->userdata('user_id');

        $result = $this->db->order_by('id desc')->limit(20)->get_where('download', array('requested_by' => $userId));

        $data['downloads'] = $result;


        $this->load->view("templates/header.php");
        $this->load->view("download/listall", $data);
        $this->load->view("templates/footer.php");

    }
    
	 
}
