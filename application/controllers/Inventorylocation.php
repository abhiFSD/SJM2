<?php

class Inventorylocation extends MY_Controller
{
    public function all($status = null)
    {
        $this->view_data['inventory_locations'] = POW\InventoryLocation::get_all();
        
        $this->default_view('inventorylocation/all');
    } 
     
    /**
     * Manage the site
     * 
     * @param string $id
     */
    public function manage($inventory_location_id = null)
    {
        $role_id = $this->session->userdata('role_id');

        if ($role_id && $role_id <= 2 && $this->input->post('save') == "Submit")
        {
            $datetime = date('Y-m-d H:i:s');

            $inventory_location = POW\InventoryLocation::with_id($this->input->post('inventory_location_id'));
            $inventory_location->assign_array($this->input->post());
            $inventory_location->country = 'AU';
            $inventory_location->date_created = $datetime;
            $inventory_location->date_updated = $datetime;
            $inventory_location->save();
            
            if ($this->input->post('inventory_location_id'))
            {
                $this->session->set_flashdata('success_message', 'Inventory Location updated successfully');
            } 
            else 
            {
                $this->session->set_flashdata('success_message', 'New Inventory Location created successfully');
            }

            redirect('inventorylocation/all');
        }

        $inventory_location = POW\InventoryLocation::with_id($inventory_location_id);

        $this->view_data['inventory_location'] = $inventory_location;
        $this->view_data['action'] = $inventory_location->id ? 'Edit' : 'Add';
        $this->view_data['status_options'] = [
            '1' => 'Active',
            '0' => 'Inactive',
        ];
        
        $this->default_view('inventorylocation/manage');
    }
     
    /**
     * Delete the site
     * @param number $id
     */
    public function delete($id)
    {
        if ($id > 0 ) 
        {
            $query = $this->db->get_where('site', array('id' => $id));
            $results = $query->result();
     
            if (count($results) > 0) 
            {
                $this->db->delete('site', array('id' => $id));
                redirect('site/all');
            } 
        }

        redirect('404');
    }
     
    /**
     * Bulk geo coding
     */
    public function geocode()
    {
        $sites = $this->db->get('site');
        foreach ($sites->result_object() as $site) 
        {
            $address = $site->name .','. $site->address1.','. $site->address2.','. $site->suburb.','. $site->state.','.$site->postcode;
            $cordinates =  $this->getLatLng($address);
            
            if ($cordinates)
            {
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
        
        if (isset($data->results[0])) 
        {
            $result  = $data->results[0];
            $cordinates ['lat'] = $result->geometry->location->lat;
            $cordinates ['lng'] = $result->geometry->location->lng;
            $cordinates ['place'] = $result->place_id;

            return $cordinates;
        }
     
        return false;
    }

}
