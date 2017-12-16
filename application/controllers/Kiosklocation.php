<?php

class KioskLocation extends MY_Controller
{
    public function all()
    {
        if ($this->input->post())
        {
            $status = $this->input->post('status');
            
            $this->view_data['kiosks'] = POW\KioskLocation::with_status($status);

            $this->load->view('kiosklocation/sections/table', $this->view_data);
        }
        else
        {
            $status = 'Active';

            $this->view_data['kiosks'] = POW\KioskLocation::with_status($status);
            $this->view_data['status'] = $status;
            $this->view_data['options'] = [
                '' => 'All',
                'Active' => 'Active',
                'Inactive' => 'Inactive',
            ];

            $this->default_view('kiosklocation/all');
        }
    }

    /**
     * Manage the products
     *
     * @param string $id
     */
    public function manage($kiosk_location_id = null)
    {
        $role_id = $this->session->userdata('role_id');
        $kiosk_location = POW\KioskLocation::with_id($kiosk_location_id);

        if ($role_id && $role_id <= 2 && $this->input->post('save') == "Submit") 
        {
            $datetime = date('Y-m-d H:i:s');

            $config['upload_path'] = BASEPATH. '/../uploads/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = '1024';
            $config['max_width']  = '1024';
            $config['max_height']  = '768';

            $this->load->library('upload', $config);

            if ( ! $this->upload->do_upload('photo'))
            {
                $this->session->set_flashdata('error_message', $this->upload->display_errors());
            } 
            else 
            {
                $upload_data = array('upload_data' => $this->upload->data());

                $kiosk_location->photo = $upload_data['upload_data']['file_name'];
            }

            $kiosk_location->assign_array($this->input->post());
            $kiosk_location->date_updated = $datetime;

            if (!$this->input->post('kiosk_location_id'))
            {
                $kiosk_location->date_created = $datetime;
                $kiosk_location->save();

                $this->session->set_flashdata('success_message', 'New kiosk location created successfully');
            }
            else
            {
                $kiosk_location->save();
                
                $this->session->set_flashdata('success_message', 'Kiosk location updated successfully');
            }

            redirect('kiosklocation/all');
        }

        $sites = POW\Site::get_all();
        $site_options = ['' => 'Select site'];

        foreach ($sites as $site)
        {
            $site_options[$site->id] = $site->state.' - '.$site->name;
        }

        $this->view_data['kiosk_location'] = $kiosk_location;
        $this->view_data['action'] = $kiosk_location->id ? 'Edit' : 'Add';
        $this->view_data['site_options'] = $site_options;
        $this->view_data['warehouse_options'] = POW\InventoryLocation::list_key_val('id', 'name', [['name', 'asc']]);
        $this->view_data['show_map'] = true;
        $this->view_data['callback'] = 'kiosk_location_init_map';
        $this->view_data['draggable'] = $role_id && $role_id <= 2 ? 'true' : 'false';
        $this->view_data['status_options'] = [
            'Active' => 'Active',
            'Inactive' => 'Inactive',
        ];

        $this->default_view('kiosklocation/manage');
    }

    /**
    * Delete the kiosk location
    * @param unknown $id
    */
    public function delete($id)
    {
        if ($id > 0 ) 
        {
            $query = $this->db->get_where('kiosk_location', array('id' => $id));
            $results = $query->result();

            if (count($results) > 0) 
            {
                $this->db->delete('kiosk_location', array('id' => $id));
                redirect('kiosk/all');
            }

            redirect('404');
        }

        redirect('404');
    }

    public function getall($site, $date)
    {
        $data = $this->db->order_by('name')->get_where('kiosk_location', array('site_id' => $site));
        $locData = array();

        foreach ($data->result() as $location) 
        {
            $locData[] = array (
                "id"	=> $location->location_id,
                "name"	=> $location->name
            );
        }

        echo json_encode($locData);
    }

    public function addNewKioskLocation()
    {
        if ($post_data = $this->input->post()) 
        {
            $data = array (
                "name" => $this->input->post('name'),
                "site_id" => $this->input->post('site_id'),
                "location_within_site"  => $this->input->post('location_within_site'),
                "status" => $this->input->post('status'),
                "warehouse_id" =>  $this->input->post('warehouse_id'),
                "date_created" => date('Y-m-d H:i:s'),
                "date_updated" => date('Y-m-d H:i:s'),
                "lat" => $this->input->post('latitude'),
                "lng" => $this->input->post('longitude'),
                "nearest_loading_dock_parking"  => $this->input->post('loading_dock'),
            );

            $config['upload_path'] = BASEPATH. '/../uploads/';
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = '1024';
            $config['max_width']  = '1024';
            $config['max_height']  = '768';

            if ($this->input->post('photo'))
            {
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
            }

            $this->load->model('Kioskmodel');
            $id = $this->Kioskmodel->addKioskLocation($data);

            $location = $this->Kioskmodel->getKioskLocationInfo($id);

            $this->session->set_flashdata('msg', 'New kiosk location created successfully');

            $response = array('status' => true, 'result' => true, 'message' => "Successful", 'name' => $location->state.' - '.$location->sitename.' - '.$location->name);

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
        }
    }

    public function getLocations()
    {
        $id = $this->input->get('id');

        $this->db->select('k.id,  k.name,s.state,s.name as sitename');
        $this->db->from('kiosk_location as k');
        $this->db->join('site as s','k.site_id = s.id');
        $conds = array('k.status'=>'Active','k.site_id' => $id );
        $this->db->where($conds);
        $this->db->order_by('k.name');
        $data['locations']= $this->db->get();

        $responses = $data['locations'];

        echo "<option value=''>Select</option>";

        foreach($responses->result_object() as $response)
        {
            echo "<option value='".$response->id."'>".$response->state.' - '.$response->sitename.' - '.$response->name."</option>";
        }
    }

}
