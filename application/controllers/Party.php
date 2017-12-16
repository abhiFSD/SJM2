<?php

class Party extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function manage()
    {
       if ($this->input->post()) 
       {
           $partyId = $this->input->post('party_id');

           $partyData = array(
               "form"           => $this->input->post('form'),
               "org_name"       => $this->input->post('name'),
               "abn"           => $this->input->post('abn'),
               "address_line_1" => $this->input->post('address_line_1'),
               "address_line_2" => $this->input->post('address_line_2'),
               "first_name" => $this->input->post('first_name'),
               "suburb" => $this->input->post('suburb'),
               "state" => $this->input->post('state'),
               "last_name" => $this->input->post('last_name'),
               "gender" => $this->input->post('gender'),
               "email" => $this->input->post('email_address'),
               "dob" => $this->input->post('dob')?$this->input->post('dob'):null,
               "country" => $this->input->post('country'),
               "postcode" => $this->input->post('postcode'),
               "land_line" => $this->input->post('landline'),
               "mobile" => $this->input->post('mobile'),
           );

           if ($partyId <= 0) {
               $this->db->insert('party', $partyData);
               $partyId = $this->db->insert_id();
           } else {
               $this->db->update('party', $partyData, array('id' => $partyId));
           }

           // if party is inserted, set the relations now
           if ($partyId) 
           {
               // clear the current relations before saving
                $this->db->delete('party_type_allocation', array('party_id' => $partyId));

                $type = $this->input->post('type');

                $data = array();

                if (is_array($type)) 
                {
                   foreach ($type as $val) {
                       $data [] = array('party_id' => $partyId, "party_type_id" => $val, "user_role_id" => 4);
                   }
                }

                $this->db->insert_batch('party_type_allocation', $data);
           }
       }

        $this->load->view("templates/header.php");
        $this->load->view('party/manage');
        $this->load->view("templates/footer.php");
    }


    public function thanks()
    {
        $params['disableMenu'] = 1;
        $params['turnOff'] = 0;
        $this->load->view("templates/header.php", $params);
        $this->load->view('customer/thankyou');
        $this->load->view("templates/footer.php", $params);
    }

    private function getNewId()
    {
        $lastId = $this->db->select('unique_code')->order_by('id','desc')->limit(1)->get('customer')->row('unique_code');

        if ($lastId) {
           $lastId = (int)str_replace('PC', '', $lastId);
        } else {
            $lastId = 0;
        }
        $lastId++;

        $lastId = sprintf("%07s",$lastId);
        $lastId = 'PC'.$lastId;
        return $lastId;
    }


    public function checkcustomer()
    {
        $email = $this->input->post('email');

        $userData = $this->db->get_where('customer', array('email_address' => $email));

        if ($userData->num_rows() > 0 ) {
            $data = $userData->result();

            $details = array(
                "id" => $data[0]->id,
                "data" => "Success"
            );
        } 
        else 
        {
            $details = array(

                "data" => "Error"
            );
        }
        echo json_encode($details);
    }


    public function getallsites($state)
    {

        $data = $this->db->order_by('name')->get_where('site', array('state' => $state, 'status' => 'Active'));
        $siteData = array();
        foreach ($data->result() as $site) {
            $siteData[] = array (

                "id"    => $site->site_id,
                "name"  => $site->name

            );
        }

        echo json_encode($siteData);
    }

    public function getalllocations($siteId = null, $date = null)
    {
        $this->db->select('d.id, l.name');
        $this->db->select("case when d.status = 'Removed' then case when uninstalled_date > '".$date."' then 1 else 0 end else 1 end as un_date  ");
        $this->db->from('kiosk_deployment as d');
        $this->db->join('kiosk_location as l', 'l.location_id = d.location_id');
        $this->db->join('site as s', 's.id = l.site_id');

        $this->db->where("s.licensor_id != 'LIC00001'");

        if ($siteId != null && $date != null) {
            $this->db->where("s.id ='". $siteId ."'");
            $this->db->where("d.installed_date < '". $date ."'");

        }
        $db = $this->db->get();
        $locations = array();
        foreach ($db->result_object() as $site)
        {

            if ($site->un_date == null || $site->un_date == 0) {

                continue;
            }


            $locations[] = array (
                'id'          => $site->id,
                'name'        => $site->name,

            );
        }

        echo json_encode($locations);
    }

    public function addNewParty()
    {
        if ($this->input->post('ajax'))
        {
            $this->load->model('Partymodel');

            $party_id = $this->Partymodel->addNewParty([
                "form"           => 'Organisation',
                "org_name"       => $this->input->post('name'),
                "display_name"   => $this->input->post('name'),
            ]);

            $licensor_id = $this->Partymodel->addAsLicensor($party_id);

            $response = ["party_id" => $party_id, "licensor_id" => $licensor_id];
    
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
        }
    }

}
