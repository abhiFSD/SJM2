<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stocktransfer extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->acl->hasAccess();
	}

	/**
	 * Main form display to the user
	 * @param string $id
	 */
	public function index($id=null)
	 {
		
	 	$this->acl->hasAccess();
	 	 
	 	$this->load->library('caspiofunction');
	     
	     $locations = $this->caspiofunction->getInventoryLocations();
	     
	     if ($this->input->post()) {
	     	
	     	 
	     	switch($this->input->post('action')) {
	     		case "Save Draft":
	     			$this->save($this->input->post(), 'Draft');
	     			break;
	     		case "Submit":
	     			$this->save($this->input->post(), 'Ordered');
	     			break;
	     		case "Pick and Pack":
	     			$this->pickandpack($this->input->post(), 'Pick and Pack');
	     			break;
	     		case "Pick and Pack - Complete":
	     			$this->pickandpack($this->input->post(), 'Picked and Packed');
	     			break;
	     		case "Dispatch":
	     			$this->dispatch($this->input->post());
	     			break;
	     		case "Receive":
	     			$this->received($this->input->post());
	     			break;
	     	}
	     	
	     	redirect('stocktransfer/listall');
	     }
	     
	     if ($id) {
	     	$data['id'] = $id;
	     	
	     	$this->load->model('StocktransferModel');
	     	$stockTransfer = $this->StocktransferModel->getById($id);
	     	$data['products'] = array();
	     	
	     	if ($stockTransfer) {
	     		
	     		$data['transfer'] = $stockTransfer;

	     		$data['transferId'] = $id;
	     		$this->load->model('InventorylocationModel');
	     		$data['currentLocation'] = $this->InventorylocationModel->getById($stockTransfer->current_location_id);
	     		$this->load->model('StocktransferitemModel');
	     		 
	     		$products = $this->StocktransferitemModel->getByTransferIdDetails($id);
	     		
	     		if ($products) {
	     			$data['products'] = $products;
	     		}
	     	} else {
	     		redirect('404');
	     	}
	     }
	     $freightLocations = $this->caspiofunction->getInventoryLocations('Freight Provider');
	    // get the warehouse details
	     
	     $data ['locations'] = $locations;
	     $data ['freightLocations'] =  $freightLocations;
	    // $data ['error'] = $errorMsg;
	     $this->load->model('InventoryitemModel');
	     $data['inventoryModelObj'] = $this->InventoryitemModel;
	     
	     
	     // load the views
	     $this->load->view("templates/header.php");
	     if ($id) {
	     	$this->load->view("stocktransfer/manage", $data);
	     } else {
         	$this->load->view("stocktransfer/index", $data);
	     }        
         $this->load->view("templates/footer.php");
	     
	 }
	 
	 public function cancel($transferId)
	 {
		$this->acl->hasAccess();
	 	$this->load->library('data');
		
		$query = $this->db->get_where('stock_transfer', array('id' => $transferId));
	 	
		if ($query->num_rows() > 0) {
			$items = $query->result();
			$data = $items[0];
			if ($data->status == 'In Transit') {
				// reverse the transactin
				$this->doMovement(array('transferId' => $data->id), $data->freight_provider_id, $data->from_location_id,"");
			}
		}
		
		$this->db->delete('stock_transfer_item', array('transfer_id' => $data->id));
		echo $this->db->last_query();
		$this->db->delete('stock_transfer', array('id' => $data->id));
		echo $this->db->last_query();
		
		
		redirect('stocktransfer/listall');
	
		
		
	 }
	 
	 private function save($post, $status)
	 {

	 	$this->load->library('data');
	 	
	 	if (!isset($post['transferId'])) {	 		
	 		
	 	 	$data= array (
		 			
		 					"from_location_id" 		=> $post['from_location'],
			 				"to_location_id" 		=> $post['to_location'],
			 				"current_location_id" 	=> $post['from_location'],
			 				"status"				=> $status,
	 	 					"notes"					=> $post['notes'],
		 					"date_created"			=> date('Y-m-d'),
	 	 					"date_updated"			=> date('Y-m-d')
		 				);
		 	$transferId = $this->data->insert('stock_transfer', $data);
	 	} else {
	 		
	 		$this->load->library('data');
		 	$this->load->model('StocktransferModel');
		 	$stockTransfer = $this->StocktransferModel->getById( $post['transferId']);
		 	 
		 	if (trim($post['notes']) != "") {
		 		$notes = $stockTransfer->notes. "<br /><br />". $post['notes'] . " on ". date('d-m-Y');
		 	} else {
		 		$notes = $post['notes'];
		 	}
		 	
	 		$data= array (
	 				"status"				=> $status,
	 				"date_updated"			=> date('Y-m-d'),
	 				"notes"					=> $notes
	 		);
	 		$this->data->update('stock_transfer', $data, array('id' => $post['transferId']));
	 		$transferId = $post['transferId'];
	 		
	 	}
	 	$this->insertToTransferItems($post, $transferId);
	 }
	 
	 /**
	  * Function used for the transaction of the item from one location to other.
	  * @param array $post
	  * @param integer $fromLocation
	  * @param integer $toLocation
	  * @param string $status
	  */
	 private function doMovement($post, $fromLocation, $toLocation, $status)
	 {
	 	
	 	$query = $this->db->get_where('stock_transfer_item', array('transfer_id' => $post['transferId']));
	 
	 	 //--------------------------------------
	 	 
	 	$itemQuery = $this->db->get_where('inventory_item', array('location_id' => $fromLocation));
	 	
	 	foreach ($itemQuery->result() as $item) {
	 		$itemData[$item->sku_id] = $item->id;
	 	}
	 	 
	 	$itemFreightQuery = $this->db->get_where('inventory_item', array('location_id' => $toLocation));
	 	
	 	$freightItems = array();
	 	if ($itemFreightQuery->num_rows() > 0) {
	 		foreach($itemFreightQuery->result() as $freightItem) {
	 			$freightItems[$freightItem->sku_id] = $freightItem->id;
	 		}
	 	}
	 	
	 	foreach ( $query->result() as $transferItem) {
	 		
	 		$sql1 = "update inventory_item set SOH=SOH-{$transferItem->item_quantity} where sku_id = {$transferItem->sku_id} and location_id = {$fromLocation}";
	 		$this->db->query($sql1);
	 		// enter to stock movement
	 		// save log data
	 		$data = array(
	 				"location_id"               => $fromLocation,
	 				"item_id"               	=> $itemData[$transferItem->sku_id],
	 				"adjustment_amount"         => $transferItem->item_quantity,
	 				"adjustment_type"           => "Transfer",
	 				"counter_location_id"       => $toLocation,
	 				"date_updated"				=> date('Y-m-d')
	 		 		
	 		);
	 	
	 		// inserted
	 		try {
	 			$this->data->insert('stock_movement_log',$data);
	 		} catch (Exception $e) {
	 			echo $e->getMessage();
	 			break;
	 		}
	 	
	 		// now add it to the second location
	 	
	 		if (array_key_exists($transferItem->sku_id, $freightItems)) {
	 			$freightInventoryId = $freightItems[$transferItem->sku_id];
	 			$sql2 = "update inventory_item set SOH=SOH+{$transferItem->item_quantity} where sku_id = {$transferItem->sku_id} and location_id = {$toLocation}";
	 			$this->db->query($sql2);
	 		} else {
	 			$newItemData = array (
	 					'location_id'	=> $toLocation,
	 					'SOH'			=> $transferItem->item_quantity,
	 					'sku_id'		=> $transferItem->sku_id
	 			);
	 		 	
	 			$this->db->insert('inventory_item', $newItemData);
	 			$freightInventoryId = $this->db->insert_id();
	 		}
	 		// log it
	 	
	 		$nextData = array (
	 				"location_id"               => $toLocation,
	 				"item_id"               	=> $freightInventoryId,
	 				"adjustment_amount"         => $transferItem->item_quantity,
	 				"adjustment_type"           => "Transfer",
	 				"counter_location_id"       => $toLocation, // @todo need to check
	 				"date_updated"				=> date('Y-m-d')
	 		 		
	 		);
	 	
	 		// inserted
	 		try {
	 			$this->data->insert('stock_movement_log',$nextData);
	 		} catch (Exception $e) {
	 			echo $e->getMessage();
	 			break;
	 		}
	 		
	 	}
	 
	 }
	 
	 
	 /**
	  * Perform the dispatch
	  * @param array $post
	  */
	 private function dispatch($post)
	 {

	 	$transferId = $post['transferId'];
	 	
		$this->load->library('data');
	 	$this->load->model('StocktransferModel');
	 	$stockTransfer = $this->StocktransferModel->getById( $post['transferId']);
	 	 
	 	if (trim($post['notes']) != "") {
	 		$notes = $stockTransfer->notes. "<br />". $post['notes'] . " on ". date('d-m-Y');
	 	} else {
	 		$notes = $post['notes'];
	 	}
	 	
	 	
	 	$time = $post['time1']. "-" . $post['time2'];
	 	
 		$data ["tracking_link"]		= $post['tracking_link'];
 		$data ["tracking_number"]	= $post['tracking_number'];
 		$data ["number_of_cartons"]	= $post['no_of_cartons'];
 		$data ["estimated_delivery"]	= $post['est_delivery_date'];
 		$data ['freight_provider_id']	= 	$post['freight_location'];
 		$data ['estimated_time']	= 	$time;
 			
 		$data ['notes']	= 	$notes;
 			
	 	
 		$this->load->library('data');
 		 
 			
 		$this->data->update('stock_transfer', $data, array('id' => $transferId));
	 	
	 	$query = $this->db->get_where('stock_transfer', array('id' => $transferId));
	 	
	 	$transfers = $query->result();
	 	$transfer = $transfers[0];
	 	
	 	$this->doMovement($post, $transfer->from_location_id, $post['freight_location'], "In Transit");
	 	$data= array (
	 			"status"				=> "In Transit",
	 			"current_location_id"	=> $post['freight_location'],
	 			"date_updated"			=> date('Y-m-d')
	 	);
	 	$this->data->update('stock_transfer', $data, array('id' => $post['transferId']));
	 	$transferId = $post['transferId'];
	 	
	 }
	 
	 
	 /**
	  * Perform the received status action
	  * @param array $post
	  */
	 private function received($post)
	 {
	 	$transferId = $post['transferId'];
	 	 
	 	$this->load->library('data');
	 	$this->load->model('StocktransferModel');
	 	$stockTransfer = $this->StocktransferModel->getById( $post['transferId']);
	 	 
	 	if (trim($post['notes']) != "") {
	 		$notes = $stockTransfer->notes. "<br /><br />". $post['notes'] . " on ". date('d-m-Y');
	 	} else {
	 		$notes = $post['notes'];
	 	}
	 	
	  	$query = $this->db->get_where('stock_transfer_item', array('transfer_id' => $transferId));
	 	 
	 	$this->doMovement($post, $transfer->freight_provider_id, $transfer->to_location_id, "Received");
	 	
	 	$data= array (
	 			"status"				=> "Received",
	 			"current_location_id"	=> $transfer->to_location_id,
	 			"notes"					=> $notes,
	 			"date_updated"			=> date('Y-m-d')
	 	);
	 	$this->data->update('stock_transfer', $data, array('id' => $post['transferId']));
	 	$transferId = $post['transferId'];
	 }
	 
	 
	 /**
	  * change the status to pick and pack
	  * @param array $post
	  * @param string $status
	  */
	 private function pickandpack($post, $status)
	 {
	 	$this->load->library('data');
	 	$this->load->model('StocktransferModel');
	 	$stockTransfer = $this->StocktransferModel->getById( $post['transferId']);
	 	 
	 	if (trim($post['notes']) != "") {
	 		$notes = $stockTransfer->notes. "<br /><br />". $post['notes'] . " on ". date('d-m-Y');
	 	} else {
	 		$notes = $post['notes'];
	 	}
	 	
 		$data = array (
 				"to_location_id"		=> $post['to_location'],
 				"status"				=> $status,
 				"notes"					=> $notes,
 				"date_updated"			=> date('Y-m-d'),
 				
 		);
 		
 
 		
 		$this->data->update('stock_transfer', $data, array('id' => $post['transferId']));
 		
 		// update for the stock transfer items
 		$this->insertToTransferItems($post,  $post['transferId']);
 	 }
	 
 	 // update the inventory items
 	 private function updateInventory($data)
 	 {
 	 	
 	 }
 	 
 	 // update the stock movement log
 	 private function updateStockMovement($data)
 	 {
 	 	
 	 }
 	 
 	 // insert data into the transfer items table 
	 private function insertToTransferItems($post, $transferId)
	 {
	 	
	 	$this->db->delete('stock_transfer_item', array('transfer_id' => $transferId));
	 	
	 	$count = $post['count'];
	 	for ($i = 0; $i < $count; $i++) {
	 		if ($post['amount'][$i] <= 0) {
	 			continue;
	 		}
	 		$transferData = array (
	 				'transfer_id'	=> $transferId,
	 				'sku_id'		=> $post['item_id'][$i],
	 				'item_quantity'	=> $post['amount'][$i],
	 				'date_created'	=> date('Y-m-d')
	 		);
	 		$this->data->insert('stock_transfer_item', $transferData);
 		}
	 }
	 
	/**
	 * Load the products
	 * 
	 * @param integer $id
	 * @param integer $toLocation
	 */
	 public function products($id, $toLocation)
	 {
	     $this->load->library('data');
	     $this->load->library('caspiofunction');
	 
	     $stockData = $this->caspiofunction->getItemsFromLocation($id);
	     $toStockData = $this->caspiofunction->getItemsFromLocation($toLocation);
	     
	     foreach ($toStockData as $toData) {
	     	$itemData[$toData['sku_id']] = $toData['SOH'];
	     	
	     	
	     }  
	     
	     $productData = array();
	     foreach ($stockData as $data) {

	         //$invData = $this->caspiofunction->getInventoryItems($toLocation,  $data['sku_id']);
	         //   print_r($invData);exit;
	         if (isset($itemData[$data['sku_id']])) {
	             $toStock = $itemData[$toData['sku_id']];
	         }else {
	             $toStock = 0;
	         }
	     
	         $productData[] = array (
	             'SKU_ID' => $data['sku_id'],
	             'SKU'    =>  $data['sku'],
	             'SKU_Name'  =>  $data['name'],
	             'From_Stock' => $data['SOH'],
	             'To_Stock'    => $toStock,
	         	 'amount'	=> 0
	            
	         );
	     }
	   
	     
	     $data = array('products' => $productData);
	 
	     $pageData = $this->load->view("stocktransfer/product", $data, true);
	     echo $pageData;
	 }
	 
	 
	 private function getItems($transferId) 
	 {
	 	
	 	
	 }
	 
	 /**
	  * Build the list from the transfer list
	  * 
	  * @param integer $id
	  * @param integer $toLocation
	  * @param integer $transferId
	  */
	 public function transferitems($id, $toLocation, $transferId)
	 {
	 	$this->load->library('data');
	 	$this->load->library('caspiofunction');
	 
	 	$stockData = $this->caspiofunction->getItemsFromLocation($id);
	 
	 	$stockTransferData = $this->data->fetch('stock_transfer_item', null, 'transfer_id = '. $transferId);
	 	foreach ($stockTransferData as $datum) {
	 		$amount[$datum['sku_id']] = $datum['item_quantity'];
	 	}
	 	
	 	$tranfer = $this->data->fetch('stock_transfer', null, 'id = '. $transferId);
	 	$stage = $tranfer[0]['status'];
	 	 
	 	
	 	$productData = array();
	 	foreach ($stockData as $data) {
	 
	 		$invData = $this->caspiofunction->getInventoryItems($toLocation,  $data['sku_id']);
	 		//   print_r($invData);exit;
	 		if ($invData) {
	 			$toStock = $invData['0']['SOH'];
	 		}else {
	 			$toStock = 0;
	 		}
	 
	 		
	 		$productData[] = array (
	 				'SKU_ID' 		=> $data['sku_id'],
	 				'SKU'    		=> $data['sku'],
	 				'SKU_Name' 		=> $data['name'],
	 				'From_Stock' 	=> $data['SOH'],
	 				'To_Stock'    	=> $toStock,
	 				'amount'		=> isset($amount[$data['sku_id']])?$amount[$data['sku_id']]:'', 
	 		);
	 	}
	 
	 
	 	$data = array('products' => $productData, 'transferId' => $transferId, 'stage' => $stage);
	 
	 	$pageData = $this->load->view("stocktransfer/product", $data, true);
	 	echo $pageData;
	 }
	 
	
	 
	 /**
	  * check the status of the transfer
	  * 
	  * @param int $to
	  * @param int $from
	  * @param string $frieght_location
	  */
	 public function checkStatus($to, $from, $frieght_location = null)
	 {
	 	
	 	$this->load->library('data');
	 	
	 //	$criteria = "from_location_id = {$to} and  to_location_id = {$from} and freight_provider_id = {$frieght_location} and status != 'Received'";
	 	$criteria = "from_location_id = {$to} and  to_location_id = {$from}  and status != 'Received'";
	
	 	$data = $this->data->fetch('stock_transfer', null, $criteria);
	
	 	if (count($data) > 0) {
	 		
	 		$return = array ('status' => 'success', 'id' => $data[0]['id'], 'stage' => $data[0]['status']);
	 	} else {
	 		$return = array ('status' => 'error');
	 	}
	 	
	 	echo json_encode($return);
	 }
	 
	 /**
	  * Function the list the stock transfer list
	  * 
	  * @param string $csv
	  */
	 public function listall($csv=false)
	 {
		$this->load->library('data');
		
		$this->db->select('st.id as ID, l1.name as From, l2.name as To, l3.name as Current, st.status as Status, 
							st.number_of_cartons as Cartons, st.estimated_delivery as EstimatedDeliveryDate, st.date_updated as DateUpdated');
		$this->db->from('stock_transfer as st');
		$this->db->join('inventory_location as l1', 'l1.id = st.from_location_id', 'left');
		$this->db->join('inventory_location as l2', 'l2.id = st.to_location_id', 'left');
		$this->db->join('inventory_location as l3', 'l3.id = st.current_location_id', 'left');
		
		$query = $this->db->get();
		
		//$data['items'] = $this->data->fetch('inventory_item',$fields, $criteria);
		if ($csv) {
			
			$this->load->dbutil();
			$this->load->helper('download');
			
			$delimiter = ",";
			$newline = "\r\n";
				
			$csvdata = $this->dbutil->csv_from_result($query, $delimiter, $newline);
			
			force_download("StockTrasfer.csv", $csvdata);
		} else {
			$data['items'] = $query->result();
			$this->load->view("templates/header.php");
			$this->load->view("stocktransfer/listall", $data);
			$this->load->view("templates/footer.php");
		}
		
	}
	 
}
