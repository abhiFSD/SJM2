<?php

class Licensor extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->acl->hasAccess();

	}

   public function all($status = 'Active')
   {

	   	if($this->session->userdata('email_address') == "" ) {
	   		redirect('/user/login');
	   	}

	
      $sql = 'SELECT
        `pta`.`id` AS `licensor_id`,
        `p`.`display_name` AS `licensor_name`
    FROM
        (`party` `p`
        LEFT JOIN `party_type_allocation` `pta` ON `p`.`id` = `pta`.`party_id`)
    WHERE
        `pta`.`party_type_id` = 2';


      if ($status != 'All') {
           $sql .= ' and status = "'.$status.'"';
      }

	   	$query = $this->db->query($sql);


//
	   	$msg = "";

	   	$this->load->view("templates/header.php");
	   	$this->load->view('licensor/all', array('licensors' => $query, 'status' => $status, 'msg' => $msg));
	   	$this->load->view("templates/footer.php");

   }

   /**
    * Manage the products
    *
    * @param string $id
    */
   public function manage($id = null)
   {


   		$msg = "";

   		if ($this->input->post('saveLicensor') == "Submit") {


   			$data = array (

                                         "form"       => "Organisation",
                                         "org_name"       => $this->input->post('name'),  
                                        "display_name"				=> $this->input->post('name'),
                                      //  "refill_sensitivity"		=> $this->input->post('refill'),
                                        // "licensor_contact_id"		=> $this->input->post('contact'),
                                        "status"                    => $this->input->post('status'),
                                        "date_created"				=> date('Y-m-d H:i:s'),
                                        "date_updated"				=> date('Y-m-d H:i:s'),

                                );

   			if ($this->input->post('licensor_id') > 0 ) {
   				// update if already present
   				$licensorId = $this->input->post('licensor_id');
   				$this->db->update('party', $data, array('id' => $licensorId));


           $status = $this->input->post('status');

           if($status == "Active")
            $status = 1;
           else
            $status = 0;


           $p = array('active'=> $status);

          $this->db->update('party_type_allocation', $p, array('party_id' => $licensorId));
    
           

   				$data["date_updated"] = date('Y-m-d H:i:s');

   				$msg = "Licensor updated successfully";
   				$this->session->set_flashdata('msg', $msg);
   				redirect('licensor/all');
   			} else {
   				// enter new

   			  //  $data ['licensor_id'] = $this->input->post('id');

           $status = $this->input->post('status');

           if($status == "Active")
            $status = 1;
           else
            $status = 0;

   				 $this->db->insert('party', $data);
           $licensor_id = $this->db->insert_id();

           $p = array('party_id' => $licensor_id , "party_type_id" =>2, "user_role_id" => 4,'active'=> $status);

           $this->db->insert('party_type_allocation', $p);

            $party_allocation_id = $this->db->insert_id();
        //   echo $party_allocation_id;

        //   die();

   				$this->session->set_flashdata('msg', 'New licensor created successfully');
   				redirect('licensor/all');

   			}


   		}

   		$data['msg'] = $msg;
   		//$this->db->order_by('organisation');
   		$data['contacts'] = $this->db->get('contact');

     // $data['licensor'] = [];
   		// if id is present, start edit mode
   		if ($id > 0 ) {

   			//$query = $this->db->get_where('party', array('id' => $id));


         $sql = 'SELECT
        `pta`.`id` AS `licensor_id`,
        `p`.`display_name` AS `licensor_name`,`p`.`status`, `p`.`id` as party_id
    FROM
        (`party` `p`
        LEFT JOIN `party_type_allocation` `pta` ON `p`.`id` = `pta`.`party_id`)
    WHERE
        `pta`.`id` = '.$id;

 
  

       $query = $this->db->query($sql);


   			$results = $query->result();
         

 
   			if (count($results) > 0) {
   
   				$licensor = $results[0];
   

   				$data['licensor'] = $licensor;
   			}

   		} else {

   		  $data['licensor_id'] = $this->getNewIdOnly();
   		}

			 $this->load->view("templates/header.php");
 	     $this->load->view('licensor/manage', $data);
 	   	 $this->load->view("templates/footer.php");



   }


   private function getNewIdOnly()
   {

       $lastId = $this->db->select('id')->order_by('id','desc')->limit(1)->get('party')->row('id');
 

       $lastId++;

      
       

       return $lastId;
   }

   private function getNewId()
   {

       $lastId = $this->db->select('licensor_id')->order_by('id','desc')->limit(1)->get('licensor')->row('licensor_id');

       $lastId = (int) str_replace('LIC', '', $lastId);

       $lastId++;

       $lastId = sprintf("%05s",$lastId);
       $lastId = 'LIC'.$lastId;

       return $lastId;
   }


   /**
    * Delete the licensor
    * @param number $id
    */
   public function delete($id)
   {
   	if ($id > 0 ) {

   		$query = $this->db->get_where('party', array('id' => $id));
   		$results = $query->result();

   		if (count($results) > 0) {

   			$this->db->delete('party', array('id' => $id));
   			redirect('licensor/all');
   		} else {
   			redirect('404');
   		}

   	} else {
   		redirect('404');
   	}

   }
/**
* function to add a licensor in the Agreement page as a pop up
*/
	 public function addNewLicensor($id = NULL)
	 {
     $msg = "";
		 if($this->input->post('ajax')) {
			 $data = array (

																			 "display_name"				=> $this->input->post('name'),
																			// "refill_sensitivity"		=> $this->input->post('refill'),
																			 // "licensor_contact_id"		=> $this->input->post('contact'),
																			 "status"                    => $this->input->post('status'),
																			 "date_created"				=> date('Y-m-d H:i:s'),
																			 "date_updated"				=> date('Y-m-d H:i:s'),

															 );

			 if ($this->input->post('licensor_id') > 0 ) {
				 // update if already present
				 $licensorId = $this->input->post('licensor_id');
				 $this->db->update('part', $data, array('id' => $licensorId));
				 $data["date_updated"] = date('Y-m-d H:i:s');

				 $msg = "Licensor updated successfully";
				 $this->session->set_flashdata('msg', $msg);
				 //redirect('licensor/all');
			 } else {
				 // enter new

					// $data ['licensor_id'] = $this->input->post('id');
				 $this->db->insert('party', $data);


				 $this->session->set_flashdata('msg', 'New licensor created successfully');
		 }
		 }

	 //$data['msg'] = $msg;
 return $msg;

}
}
