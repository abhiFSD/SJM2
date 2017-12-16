<?php

class Agreement extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->acl->hasAccess();

	}

   public function all($status = 'Active')
   {

   	    if ($this->session->userdata('email_address') == "" ) {
	   		redirect('/user/login');
	   	}

	   	$this->db->select('a.id,  a.name, a.day_due, a.start_date, a.end_date, a.fixed_fee_exGST,
	   	                    a.commission_1_rate, a.commission_2_rate,a.commission_1_threshold,a.commission_2_threshold,a.status,a.licensor_id,p.display_name');
	   	$this->db->from('license_agreement as a');

			 $this->db->join('party_type_allocation as pta','pta.id = a.licensor_id','left');
      $this->db->join('party as p','p.id = pta.party_id','left');

	 

	   	if ($status != 'All') {
	   	    $this->db->where("a.status = '". $status ."'");
	   	}

	   	$query = $this->db->get();
 //print_r($this->db->last_query());
	   	//$query = $this->db->get_where('site', array('status' => $status));
	   	$msg = "";

	   	$this->load->view("templates/header.php");
	   	$this->load->view('agreement/all', array('agreements' => $query, 'status' => $status, 'msg' => $msg));
	   	$this->load->view("templates/footer.php");

   }

   /**
    * Manage the site
    *
    * @param string $id
    */
   public function manage($id = null)
   {


   		$msg = "";
    //  $license_id = $this->Licensor_Model->getId();

   		if ($this->input->post('save') == "Submit") {

   			$data = array (

   			          "name"						=> $this->input->post('name'),
   								"licensor_id"				=> $this->input->post('licensor_id'),
   								"status"					=> $this->input->post('status'),
   						//		"start_date"				=> $this->input->post('start_date'),
   					//			"end_date"		    		=> $this->input->post('end_date'),

   							);


            if ($this->input->post('fixed') != "") {
                $data['fixed_fee_exGST'] = $this->input->post('fixed');
            }


            if ($this->input->post('commission1') != "") {
							// print_R($this->input->post('commission1')/100);
							// exit;
                $data['commission_1_rate'] = ($this->input->post('commission1')/100);
                $data['commission_1_threshold'] = $this->input->post('commission1_threshold');

            }
            if ($this->input->post('commission2') != "") {
                $data['commission_2_rate'] = ($this->input->post('commission2')/100);
                $data['commission_2_threshold'] = $this->input->post('commission2_threshold');
            }

            if ($this->input->post('agreement_id') > 0 ) {
   				// update if already present
   				$agreementId = $this->input->post('agreement_id');

   				$this->db->update('license_agreement', $data, array('id' => $agreementId));

   				$msg = "Agreement updated successfully";
   				$this->session->set_flashdata('msg', $msg);
   				redirect('agreement/all');
   			} else {
   				// enter new

   			    $data['agreement_id'] = $this->input->post('agreement_id');
						//print_r($data);
   			    $this->db->insert('license_agreement', $data);

   			    $this->session->set_flashdata('msg', 'New agreement created successfully');
   			    redirect('agreement/all');

   			}


   		}



   		$data['msg'] = $msg;

   		// if id is present, start edit mode
   		if ($id > 0 ) {

   			$query = $this->db->get_where('license_agreement', array('id' => $id));
   			$results = $query->result();

   			if (count($results) > 0) {
   				$agreement = $results[0];
   				$data['agreement'] = $agreement;
   			}

   		} else {
   		    $data['agreement_id'] = $this->getNewIdOnly('id','license_agreement','','');
   		}

			$this->load->model('Partymodel');

   		$data['licensors'] = $this->Partymodel->getLicensors();
			$data['license_id']= $this->Partymodel->getnextId();

   		$this->load->view("templates/header.php");
	   	$this->load->view('agreement/manage', $data);
	   	$this->load->view("templates/footer.php");

   }

   private function getNewIdOnly($column_name,$table_name,$lead_char = NULL,$leadingzeros = NULL)
   {

       $lastId = $this->db->select($column_name)->order_by('id','desc')->limit(1)->get($table_name)->row($column_name);

      // $lastId = (int) str_replace($lead_char, '', $lastId);

       $lastId++;

       //$lastId = sprintf($leadingzeros,$lastId);
     //  $lastId = $lead_char.$lastId;
       return $lastId;
   }


   private function getNewId($column_name,$table_name,$lead_char = NULL,$leadingzeros = NULL)
   {

       $lastId = $this->db->select($column_name)->order_by('id','desc')->limit(1)->get($table_name)->row($column_name);

       $lastId = (int) str_replace($lead_char, '', $lastId);

       $lastId++;

       $lastId = sprintf($leadingzeros,$lastId);
       $lastId = $lead_char.$lastId;
       return $lastId;
   }

	 public function addNewAgreement()
	 {
	 	$msg = "";
	 	 if ($this->input->post('ajax')) {

	 			$data = array (

	 								"name"						=> $this->input->post('la_name'),
	 								"licensor_id"				=> $this->input->post('la_licensor_name'),
									"status"					=> $this->input->post('la_status'),
									 	"start_date"				=> $this->input->post('la_start_date'),
									 	"end_date"		    		=> $this->input->post('la_end_date'),

	 							);


	 				 if ($this->input->post('la_fixed') != "") {
	 						 $data['fixed_fee_exGST'] = $this->input->post('la_fixed');
	 				 }


	 				 if ($this->input->post('la_commission1') != "") {
	 						 $data['commission_1_rate'] = $this->input->post('la_commission1');
	 						 $data['commission_1_threshold'] = $this->input->post('la_commission1_threshold');
	 				 }
	 				 if ($this->input->post('la_commission2') != "") {
	 						 $data['commission_2_rate'] = $this->input->post('la_commission2');
	 						 $data['commission_2_threshold'] = $this->input->post('la_commission2_threshold');
	 				 }


	 					$this->db->insert('license_agreement', $data);


	 		}

	 		$data['msg'] = $msg;
	 }

	 public function getLicenseAgreements()
	 {
     $id = $this->input->get('id');
		 $data['license_agreements']= $this->db->get_where('license_agreement',array('licensor_id'=>$id));

		 $responses = $data['license_agreements'];

      echo "<option value=''>Select</option>";
		 foreach($responses->result_object() as $response)
		 {

			 echo "<option value='".$response->id."'>".$response->name."</option>";
		 }


	 }


}
