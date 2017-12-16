<?php if (!defined('BASEPATH')) die();
class Contact extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->acl->hasAccess();
	}
	
        public function index()
	{
		
		if($this->session->userdata('email_address') == "" ) {
			redirect('/auth/login');
		} 
		
		$role = $this->session->userdata('role_id');
		$userId = $this->session->userdata('user_id');
		
		
            $data = array();

            $data['contacts'] = $this->db->get('contact');

           // $data['contacts'] = $query->result();
	  
	   
            $this->load->view('templates/header', array('role' => $this->session->userdata('role_id'), 'name' => $this->session->userdata('name')));
            $this->load->view('contact/list', $data);
            $this->load->view('templates/footer');
	}
	
	
	
	     
        
        public function manage($id = null)
        {
   	
            $msg = "";

            if ($this->input->post('save') == "Submit") {

                    $data = array (

                                    "first_name"		=> $this->input->post('first_name'),
                                    "last_name"			=> $this->input->post('last_name'),
                                 //   "contact_id"		=> $this->input->post('contact_id'),
                                    "licensor_id"		=> $this->input->post('licensor_id'),
                        
                                 //   "organisation"              => $this->input->post('organisation'),
                                    
                                   );

                    if ($this->input->post('id') > 0 ) {
                            // update if already present
                            $id = $this->input->post('id');
                            $this->db->update('contact', $data, array('id' => $id));	

                            $msg = "Contact details updated successfully";
                            $this->session->set_flashdata('msg', $msg);
                            redirect('contact');
                    } else {
                            // enter new 
                         //   $data ['contact_id'] = $this->input->post('contact_id');
                            $this->db->insert('contact', $data);

                            $this->session->set_flashdata('msg', 'Contact details created successfully');
                            redirect('contact');
                    }


            }

            $data['msg'] = $msg;

            // if id is present, start edit mode
            if ($id > 0 ) {

                    $query = $this->db->get_where('contact', array('id' => $id));
                    $results = $query->result();

                    if (count($results) > 0) {
                            $contact = $results[0];
                            $data['contact'] = $contact;	
                    }

            } else {

              $data['contact_id'] = $this->getNewId();
            }

            $this->load->model('Partymodel');


           // $this->db->order_by('name', 'asc');
           // $licensor_data = $this->db->get('licensor');
            $data['licensors'] = $this->Partymodel->getLicensors();

            $this->load->view("templates/header.php");
            $this->load->view('contact/manage', $data);
            $this->load->view("templates/footer.php");
	   	
        }
        
        private function getNewId()
        {

            $lastId = $this->db->select('id')->order_by('id','desc')->limit(1)->get('contact')->row('id');

        //    $lastId = (int) str_replace('CID', '', $lastId);

            $lastId++;

          //  $lastId = sprintf("%04s",$lastId);
            //$lastId = 'CID'.$lastId;
            return $lastId;
        }


}

/* End of file frontpage.php */
/* Location: ./application/controllers/frontpage.php */
 