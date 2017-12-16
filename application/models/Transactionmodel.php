<?php

class Transactionmodel extends CI_Model
{
	
	const TABLE = 'transaction';
	
	 
	/**
	 * 
	 * Get the location based ont he ID
	 * 
	 * @param integer $id
	 * @return string
	 */
	public function getById($id)
	{
		$query = $this->db->get_where(self::TABLE, array('nayax_transaction_id' => $id));
		
		if ($query->num_rows() > 0) {
			$data = $query->result();
			
			return $data[0];
		} else {
			
			return false;
		}
	}
	

        /**
     * 
     * Check txn ID
     * 
     * @param integer $id
     * @return string
     */
    public function getTxnById($id)
    {
        $query = $this->db->get_where("txn_feed", array('nayax_transaction_id' => $id));
        
        print_r($this->db->last_query());

        if ($query->num_rows() > 0) {
            $data = $query->result();
            
            return $data[0];
        } else {
            
            return false;
        }
    }


    public function insertTxnFeed(  $data, $details,$transaction_time, $auth_time,$settlement_time )

    {
         if (!$this->getTxnById($data['TransactionId'])) {

              //  echo "here.......";
                $Trxndata = array(

                'date_authorised'       => $transaction_time->format('Y-m-d H:i:s'), 
                'kiosk_number'              => $details['Operator Identifier'],
                'position'              => $details['Product PA Code'],
                'sku_value'         => $details['Catalog Number'],
                'amount_auth'                 => $details['Authorization Value'],
                'currency'              => $details['Currency'],
                'payment_method'        => $details['Payment Method Description'],
                'payment_desc'          => $details['Recognition Description'],
                'card_method'           => $details['Card Type'],
                'card_type'                 => $details['Brand'],
                'first_4_digits'        => $details['Card First 4 Digits'],
                'last_4_digits'         => $details['Card Last 4 Digits'],
                'nayax_transaction_id'        => $data['TransactionId'],
                'confirmation_id'               => $details['PayServTransid'],
                'cancel_type'                 => $details['Cancel Type'],
                'remote_dispense_phone'       => $details['CLI'],
                'amount_mach'                 => $details['Machine Price'],
                'op_button_code'              => $details['OP Button Code'],
                'map_product_code'            => $details['Product PA Code'],
                'void_flag'                 => $data['Void']
                  );

 
                print_r( $Trxndata);
              $id=  $this->db->insert('txn_feed',  $Trxndata);
              echo $id;

             print_r( $this->db->last_query() ); 
                return $this->db->insert_id();
            }

        return 0;
    }
	
	public function insert($data)
	{
	    
      

          $details = get_object_vars($data['Data']);
          $transaction_time = new DateTime($details['Machine AuTime']);
          $auth_time = new DateTime($details['Authorization Time']);
          $settlement_time = new DateTime($details['Settlement Time']);

         // print_r( $this->getById($data['TransactionId']) );

          $this->insertTxnFeed( $data,$details,$transaction_time, $auth_time,$settlement_time );

	    if (!$this->getById($data['TransactionId'])) {


    	    $transaction = array (
    	            
    	                            'nayax_transaction_id'        => $data['TransactionId'],
    	                            'payment_method_id'           => $data['PaymentMethodId'],
    	                            'site_id'                     => $data['SiteId'],
    	                            'transaction_machine_time'    => $transaction_time->format('Y-m-d H:i:s'),
    	                            'product_pa_code'             => $details['Product PA Code'],
    	                            'product_catalog_number'      => $details['Catalog Number'],
    	                            'amount'                      => $details['Default Price'],
    	                            'merchant_id'                 => $details['Merchant ID'],
    	                            'operator_id'                 => $details['Operator Identifier'],
    	                            'machine_name'                => $details['Machine Name'],
    	                            'log'                         => json_encode($data),
    	                            'created_date'                => date('Y-m-d H:i:s') 
    	                  
    	                        );
    	    
    	    
    	    $this->load->model('DeploymentModel');
            $deploymentId  = $this->DeploymentModel->getDeploymentByDate($details['Operator Identifier'], $transaction_time);

            $transaction['deployment_id'] = $deploymentId;
            
    	    $this->db->insert(self::TABLE, $transaction);
    	    $id = $this->db->insert_id();
    	    // insert into the payment table as well.
    	    
    	    if ($id) { 
        	    $payment_details = array(
        	            
        	            'transaction_id'     => $id,
        	            'payment_method'     => $details['Payment Method Description'],
        	            'brand'              => $details['Brand'],
        	            'first_4_digits'     => $details['Card First 4 Digits'],
        	            'last_4_digits'      => $details['Card Last 4 Digits'],
        	            'currency'           => $details['Currency'],
        	            'card_type'         => $details['Card Type'],
        	            'authorization_date' => $auth_time->format('Y-m-d H:i:s'), 
        	            'settlement_time'    => $settlement_time->format('Y-m-d H:i:s'),
        	            'card_string'        => $details['Card String']   
        	    );
        	    
        	    $this->db->insert('transaction_payment_details', $payment_details);




        	    return $id;
    	    } else {
    	        return false;
    	    }
    	}
	
	}







	
	
	
}