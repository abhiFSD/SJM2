<?php

class Deployment extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        //$this->acl->hasAccess();
    }

    public function all($status = 'Installed') //@todo made it all for test phase
    {
        $this->load->model('Kioskmodel');

        if ('Installed' == $status)
        {
            $deployments = POW\KioskDeployment::get_all(['Installed', 'Install Scheduled']);
        }
        elseif ('Removed' == $status)
        {
            $deployments = POW\KioskDeployment::get_all(['Removed', 'Removal Scheduled']);
        }
        else
        {
            $deployments = POW\KioskDeployment::get_all();
        }

        $this->load->view("templates/header.php");
        $this->load->view('deployment/all', array('deployments' => $deployments, 'status' => $status));
        $this->load->view("templates/footer.php");
    }

   /**
    * Manage the deployments
    *
    * @param string $id
    */
    public function manage($id = null)
    {
        $msg = "";

        // each kiosk can only be deployed once
        if ($this->input->post('save') == "Submit" 
            && $this->input->post('agreement_status') == 'Installed'
            && POW\KioskDeployment::is_machine_deployed($this->input->post('machine_id'), $this->input->post('deployment_id'))
        )
        {
            $msg = 'The selected kiosk is currently showing in another deployment with status = "Installed". 
            Please mark the status of this deployment as "Install scheduled" until the other deployment\'s status has been set to "Removed".';
        }
        elseif ($this->input->post('save') == "Submit") 
        {
            $data = array (
                "license_agreement_id" => $this->input->post('agreement_id'),
                "machine_id" => $this->input->post('machine_id'),
                "status" => $this->input->post('agreement_status'),
                "location_id" => $this->input->post('location_id'),
                "date_created" => date('Y-m-d H:i:s'),
                "date_updated" => date('Y-m-d H:i:s'),
                "installed_date" => $this->input->post('start_date'),
                "uninstalled_date" =>$this->input->post('end_date')
            );

            $config['upload_path'] = BASEPATH. '/../uploads/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = '1024';
            $config['max_width']  = '1024';
            $config['max_height']  = '768';

            $this->load->library('upload', $config);

            if ( ! $this->upload->do_upload('photo'))
            {
                $error = array('error' => $this->upload->display_errors());
            }
            else
            {
                $upload_data = array('upload_data' => $this->upload->data());
                $data['photo'] = $upload_data['upload_data']['file_name'];
            }

            // @todo coditions commented for temperory testing
            if ($this->input->post('status') == "Installed" || $this->input->post('status') == "Removal Scheduled") 
            {
                $data['installed_date'] = $this->input->post('start_date');
            } 
            else if ($this->input->post('status') == "Removed") 
            {
                $data['uninstalled_date'] = $this->input->post('end_date');
            }

            if ($this->input->post('deployment_id') > 0 ) 
            {
                $kiosk_deployment = POW\KioskDeployment::with_id($this->input->post('deployment_id'));
                $kiosk_deployment->assign_array($data);
                $kiosk_deployment->save();

                $this->session->set_flashdata('success_message', 'Deployment updated successfully');
            } 
            else 
            {
                $kiosk_deployment = new POW\KioskDeployment();
                $kiosk_deployment->assign_array($data);
                $kiosk_deployment->save();

                $this->session->set_flashdata('success_message', 'New deployment created successfully');
            }

            return redirect('deployment/all');
        }

        $data['msg'] = $msg;
        $data['licensor_id'] = 0;
        $data['locations'] = [];
        $data['sites'] = [];

        // if id is present, start edit mode
        if ($id > 0 ) {
            $data['deployment'] = $deployment = POW\KioskDeployment::with_id($id);

            $licensor_id = $deployment->kiosk_location->site->licensor_id;

            $data['licensor_id'] = $licensor_id;

            if ($deployment->id) 
            {
                $data['sites'] = POW\Site::with_licensor_id($licensor_id);
                $data['locations'] = POW\KioskLocation::of_licensor($licensor_id);
                $data['machines'] = array_merge([POW\Kiosk::with_id($deployment->machine_id)], POW\Kiosk::available_machines($deployment->machine_id));
            }
        }
        else 
        {
            $data['deployment'] = new POW\KioskDeployment();
            $data['machines'] = POW\Kiosk::available_machines();
        }

        $data['licensors'] = POW\PartyTypeAllocation::get_licensors();
        $data['warehouses']= POW\InventoryLocation::get_all();
        $data['models']= $this->db->get('kiosk_model');

        $this->load->view("templates/header.php");
        $this->load->view('deployment/manage', $data);
        $this->load->view("templates/footer.php");
    }

   /**
    * Uninstall the installation
    *
    * @param integer $id
    */
    public function uninstall($id)
    {
        if ($id > 0 ) {

            $query = $this->db->get_where('kiosk_deployment', array('id' => $id));
            $results = $query->result();

            if (count($results) > 0) {
                $data['uninstalled_date'] = date('Y-m-d H:i:s');
                $data['status'] = 'Inactive';

                $this->db->update('kiosk_deployment', $data, array('id' => $id));
                redirect('deployment/all');
            } else {
                redirect('404');
            }

        } else {
            redirect('404');
        }
    }

   /**
    * Delete the deployment
    * @param unknown $id
    */
   public function delete($id)
   {
        if ($id > 0 ) {

            $query = $this->db->get_where('kiosk_deployment', array('id' => $id));
            $results = $query->result();

            if (count($results) > 0) {

                $this->db->delete('kiosk_deployment', array('id' => $id));
                redirect('deployment/all');
            } else {
                redirect('404');
            }

        } else {
            redirect('404');
        }

   }

   /**
    * get the location in JSON format
    * @return string
    */
   public function locations($siteId = null, $date = null)
   {
       $this->db->select('d.id, s.name, s.address, s.city,  s.state,s.postcode, l.name as locname, l.lat, l.lng, l.location_within_site');
       $this->db->select("case when d.status = 'Removed' then uninstalled_date else null end as un_date  ");
       $this->db->from('kiosk_deployment as d');
       $this->db->join('kiosk_location as l', 'l.id = d.location_id');
       $this->db->join('site as s', 's.id = l.site_id');
       $this->db->join('licensor as lr', 's.licensor_id = lr.id');
       $this->db->where("lr.licensor_id != 'LIC00001'");

       if ($siteId != null && $date != null) {
           $this->db->where("s.id ='". $siteId ."'");
           $this->db->where("d.installed_date > '". $date ."'");

       } else {
           $this->db->where("d.status in ('Installed')");
       }

       $db = $this->db->get();

       $locations = array();
       foreach ($db->result_object() as $site)
       {

            if (!($site->un_date != null && $site->un_date < $date)) {
                continue;
            }

            $locations[] = array (
                'id' => $site->id,
                'name' => $site->locname,
                'address' => $site->location_within_site,
                'address2' => $site->address. ', '. $site->city,
                'lat' => $site->lat,
                'lng' => $site->lng,
                'state' => $site->state,
                "postal" =>  $site->postcode
            );
       }

       header('Content-type: application/json');

       echo json_encode($locations);
   }

}
