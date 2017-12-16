<?php

class Machine extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->acl->hasAccess();
	}

   public function all()
   {

   		$this->load->library('pagination');

	   	if($this->session->userdata('email_address') == "" ) {
	   		redirect('/user/login');
	   	}

	   	$query = $this->db->get('machine');

	   	$msg = "";

	   	$this->load->view("templates/header.php");
	   	$this->load->view('machine/all', array('machines' => $query, 'msg' => $msg));
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

   		if ($this->input->post('save') == "Submit") {

   			$data = array (

   			          "model_id"					=> $this->input->post('model_id'),
   								"distributor"				=> $this->input->post('distributor'),
   								"labour_warranty"		=> $this->input->post('labour_warranty'),
			   					"part_warranty"		  => $this->input->post('part_warranty'),
   			          "phone_number"      => $this->input->post('phone_number'),
			   					"date_created"			=> date('Y-m-d H:i:s'),
   								"date_updated"			=> date('Y-m-d H:i:s'),
   								"number"            => $this->input->post('id')
   							);



   			$modelData = $this->db->get_where('machine_model', array('model_id' => $this->input->post('model_id')));
   			$model = $modelData->result();

   			if (count($model) > 0) {
   			    $data['model'] = $model[0]->name;
   			}
   			if ($this->input->post('machine_id') > 0 ) {
   				// update if already present
   				$machineId = $this->input->post('machine_id');
   				$this->db->update('machine', $data, array('id' => $machineId));
   				$data["date_updated"] = date('Y-m-d H:i:s');

   				$msg = "Machine updated successfully";
   				$this->session->set_flashdata('msg', $msg);
   				redirect('machine/all');
   			} else {
   				// enter new
   				//$data['number'] = $this->input->post('id');
   				$this->db->insert('machine', $data);

   				$this->session->set_flashdata('msg', 'New machine created successfully');
   				redirect('machine/all');
   			}

   		}

   		$data['msg'] = $msg;

   		// if id is present, start edit mode
   		if ($id > 0 ) {
   		    $this->db->order_by('model');
   			$query = $this->db->get_where('machine', array('id' => $id));
   			$results = $query->result();

   			if (count($results) > 0) {
   				$machine = $results[0];
   				$data['machine'] = $machine;
   			}

   		} else {
   		    $data['machine_id'] = $this->getNewId();
   		}

   		$data['models'] = $this->db->get('machine_model');
       

   		$this->load->view("templates/header.php");
	   	$this->load->view('machine/manage', $data);
	   	$this->load->view("templates/footer.php");

   }

   private function getNewId()
   {

       $lastId = $this->db->select('number')->order_by('number','desc')->limit(1)->get('machine')->row('number');

       $lastId = (int) str_replace('MPP', '', $lastId);

       $lastId++;

       $lastId = sprintf("%03s",$lastId);
       $lastId = 'MPP'.$lastId;
       return $lastId;
   }


   /**
    * Delete the machine
    * @param number $id
    */
   public function delete($id)
   {
   	if ($id > 0 ) {

   		$query = $this->db->get_where('machine', array('id' => $id));
   		$results = $query->result();

   		if (count($results) > 0) {

   			$this->db->delete('machine', array('id' => $id));
   			redirect('machine/all');
   		} else {
   			redirect('404');
   		}

   	} else {
   		redirect('404');
   	}

   }

    /**
     *
     * function to deal ajax request which check for the unique machine number
     * @param $value
     */
   public function isunique($value)
   {
       $query = $this->db->get_where('machine', array('number' => $value));
       $results = $query->result();

       if (count($results) > 0) {
           echo $this->getNewId();
       } else{
           echo 1;
       }
   }

}
