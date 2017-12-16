<?php

class Site extends MY_Controller
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

        $this->load->model('Sitemodel');

        $sites = $this->Sitemodel->get_all_sites($status);
        $msg = "";

        $this->load->view("templates/header.php");
        $this->load->view('site/all', array('sites' => $sites, 'status' => $status, 'msg' => $msg));
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
                "name"                      => $this->input->post('name'),
                "address"                   => $this->input->post('address'),
                "country"                   => 'AU',
                "city"                      => $this->input->post('city'),
                "state"                     => $this->input->post('state'),
                "postcode"                  => $this->input->post('postcode'),
                "licensor_id"               => $this->input->post('licensor_id'),
                "days_per_week"         => $this->input->post('day_per_week'),
                "category"                  => $this->input->post('category'),
                "status"                    => $this->input->post('status'),
                "date_created"              => date('Y-m-d H:i:s'),
                "date_updated"              => date('Y-m-d H:i:s'),
                "security_phone_number"     => $this->input->post('security_phone_number'),
                "concierge_phone"           => $this->input->post('concierge_phone')
            );


            $contactData = $this->db->get_where('contact', array('licensor_id' => $this->input->post('licensor_id')));
            $contact = $contactData->result();

            if (count($contact) > 0) {
                $data['site_contact_id'] = $contact[0]->contact_id;
            }

            if ($this->input->post('site_id') > 0 ) {
                // update if already present
                $siteId = $this->input->post('site_id');
                $data["date_updated"] = date('Y-m-d H:i:s');

                $this->db->update('site', $data, array('id' => $siteId));

                $msg = "Site updated successfully";
                $this->session->set_flashdata('msg', $msg);
                redirect('site/all');
            } else {
                // enter new
                $this->db->insert('site', $data);
                $this->session->set_flashdata('msg', 'New site created successfully');
                redirect('site/all');
            }
        }

        $data['msg'] = $msg;

        // if id is present, start edit mode
        if ($id > 0 ) {

            $query = $this->db->get_where('site', array('id' => $id));
            $results = $query->result();

            if (count($results) > 0) {
                $site = $results[0];
                $data['site'] = $site;
            }
        }

        $this->load->model('Partymodel');
        $data['licensors'] = $this->Partymodel->getLicensors();
        $data['license_id']= $this->Partymodel->getnextId();

        $this->load->view("templates/header.php");
        $this->load->view('site/manage', $data);
        $this->load->view("templates/footer.php");
   }

   /**
    * Delete the site
    * @param number $id
    */
   public function delete($id)
   {
    if ($id > 0 ) {

        $query = $this->db->get_where('site', array('id' => $id));
        $results = $query->result();

        if (count($results) > 0) {

            $this->db->delete('site', array('id' => $id));
            redirect('site/all');

        } else {
            redirect('404');
        }

    } else {
        redirect('404');
    }

   }

   /**
    * Bulk geo coding
    */
   public function geocode()
   {

      $sites = $this->db->get('site');
      foreach($sites->result_object() as $site) {

        $address = $site->name .','. $site->address1.','. $site->address2.','. $site->suburb.','. $site->state.','.$site->postcode;
        $cordinates =  $this->getLatLng($address);

        if ($cordinates) {

           $data['lat'] = $cordinates['lat'];
           $data['lng'] = $cordinates['lng'];
           $data['place_id'] = $cordinates['place'];

           $this->db->update('site', $data, array('id' => $site->id));

        }
      }
   }

   private function getLatLng($address)
   {

      $address = urlencode($address);
      $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$address. "&key=AIzaSyDiA1bCNTFIEdXtyd0LWkR1ZtsserGkXIA";
      $data = file_get_contents($url);

      $data = json_decode($data);

      if (isset($data->results[0])) {
        $result  = $data->results[0];
        $cordinates ['lat'] = $result->geometry->location->lat;
        $cordinates ['lng'] = $result->geometry->location->lng;
        $cordinates ['place'] = $result->place_id;
        return $cordinates;
      }

      return false;
   }

    public function addNewSite()
    {
        if ($this->input->post()) 
        {
            $data = array (
                "name"                  => $this->input->post('name'),
                "address"               => $this->input->post('address'),
                "country"               => 'AU',
                "city"                  => $this->input->post('city'),
                "state"                 => $this->input->post('state'),
                "postcode"              => $this->input->post('postcode'),
                "licensor_id"           => $this->input->post('licensor_id'),
                "days_per_week"         => $this->input->post('days_per_week'),
                "category"              => $this->input->post('category'),
                "status"                => $this->input->post('status'),
                "date_created"          => date('Y-m-d H:i:s'),
                "date_updated"          => date('Y-m-d H:i:s'),
                "security_phone_number" => $this->input->post('security_phone_number'),
                "concierge_phone"       => $this->input->post('concierge_phone')
            );

            $contactData = $this->db->get_where('contact', array('licensor_id' => $this->input->post('licensor_id')));
            $contact = $contactData->result();

            if (count($contact) > 0) {
                $data['site_contact_id'] = $contact[0]->id;
            }

            $this->db->insert('site', $data);

            $this->session->set_flashdata('msg', 'New site created successfully');
        }
    }

    public function getSitesByState()
    {
        $states = $this->input->get('id');
        $states = $states && 'null' != $states ? explode(',', $states) : [];

        foreach(POW\Site::in_state($states) as $site)
        {
            echo "<option value='".$site->id."' data-id='".$site->id."' selected>".$site->name."</option>\n";
        }
    }

    public function getSitesByLicensor($party_id)
    {
        $this->load->model('Sitemodel');
        $sites = $this->Sitemodel->with_party_id($party_id);

        echo "<option value=''>Select</option>";
        
        if (count($sites)) 
        {
            foreach($sites as $site)
            {
                echo "<option value='".$site->name."' data-id='".$site->id."'>".$site->name."</option>";
            }
        } 
    }

}
