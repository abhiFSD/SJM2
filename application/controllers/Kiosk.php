<?php

class Kiosk extends MY_Controller
{
    public function all()
    {
        $this->view_data['kiosks'] = POW\Kiosk::get_all();

        $this->default_view('kiosk/all');
    }

    public function manage($id=null)
    {
        $role_id = $this->session->userdata('role_id');
        $this->load->model('Kioskmodel');

        $kiosk = POW\Kiosk::with_id($id);

        if ($role_id && $role_id <= 2 && $this->input->post('save') == 'Submit')
        {
            $datetime = date('Y-m-d H:i:s');
            $is_new = false;

            $kiosk->assign_array($this->input->post());
            $kiosk->date_updated = $datetime;
            
            if (!$kiosk->id)
            {
                $is_new = true;
                $kiosk->date_created = $datetime;
            }

            $kiosk->save();

            $this->session->set_flashdata('success_message', 'Kiosk has been '.($is_new ? 'added' : 'updated').' successfully');

            redirect('kiosk/all');
        }

        $kiosk_model_options = ['' => 'Select'];
        foreach (POW\KioskModel::get_all() as $kiosk_model)
        {
            $kiosk_model_options[$kiosk_model->id] = $kiosk_model->name.'-'.$kiosk_model->make;
        }

        $party_type_allocation_options = ['' => 'Select'];
        foreach (POW\PartyTypeAllocation::with_party_type_id(9) as $party_type_allocation)
        {
            $party_type_allocation_options[$party_type_allocation->party_id] = $party_type_allocation->display_name;
        }

        if ($kiosk->date_purchased)
        {
            $date = new DateTime($kiosk->date_purchased);
            $date_purchased = $date->format('Y-m-d')."T".$date->format('H:i:s');
        }
        else
        {
            $date_purchased = date('Y-m-d')."T09:00:00";
        }

        $this->view_data['body_id'] = 'kiosk-manage';
        $this->view_data['kiosk'] = $kiosk;
        $this->view_data['kiosk_number'] = $kiosk->id ? $kiosk->number : POW\Kiosk::new_number();
        $this->view_data['kiosk_model_options'] = $kiosk_model_options;
        $this->view_data['party_type_allocation_options']= $party_type_allocation_options;
        $this->view_data['kioskValues'] = $this->Kioskmodel->getAllKioskConfigurationOptions(); // to get all values for drop down for kiosk configurations
        $this->view_data['offering_attribute_allocation'] = $this->Kioskmodel->checkOfferingAttributeAllocation($id);
        $this->view_data['action'] = $kiosk->id ? 'Edit' : 'Add';
        $this->view_data['status_options'] = ['Inactive' => 'Inactive', 'Active' => 'Active'];
        $this->view_data['date_purchased'] = $date_purchased;

        $this->default_view('kiosk/manage');
    }

    /**
     * Function to find if the given machine number is unique
     * @param string machine number
     *
     *
     */
    public function is_unique($kiosk_number, $kiosk_id = 0)
    {
        $kiosk = POW\Kiosk::with_id($kiosk_id);

        print POW\Kiosk::number_is_unique($kiosk_number, $kiosk_id) ? 'true' : ($kiosk->id && $kiosk->number ? $kiosk->number : POW\Kiosk::new_number());
    }

    /**
     * function to get the kiosk based on filters
     *
     */
    public function findKiosk()
    {
        $this->load->model('Kioskmodel');
        if ($this->input->get('json'))
        {
            $cond = $this->input->get('json');

            $item_name =$this->input->get('item_name');
            $new_conds = json_decode($cond);
            $data['selections'] = $this->Kioskmodel->getKioskByCondition($new_conds,$item_name);

            if ($this->input->get('config'))
            {
                $this->load->model('ConfigItemModel');

                $values = $this->ConfigItemModel->getValueOptions($this->input->get('config'));

                if ($values){
                    $data['new_attribute_values'] = explode(';',$values->value_options);
                }
            }

            $this->load->view('kiosk/batchajax',$data);
        }
    }

    /**
     * Function to queue configurations and delete all previous queued configurations
     *
     */
    public function deleteQueued()
    {
        $this->load->model('Kioskmodel');

        if ($this->input->get('json'))
        {
            $deleteConfigs = json_decode($this->input->get('json'));
            $deleteConfigs['datetime'] = $this->input->get('datetime');
            $deleted = $this->Kioskmodel->queueKiosks($deleteConfigs);
            
            foreach($deleteConfigs as $kiosk)
            {
                $kiosk_ids[] = $kiosk[2];
            }

            $item_name =$this->input->get('item_name');

            if ($deleted)
            {
                if ($this->input->get('filters')) 
                {
                    $selections = $this->Kioskmodel->getKioskByCondition(json_decode($this->input->get('filters')),$item_name );
                    if ($this->input->get('config'))
                    {
                        $this->load->model('ConfigItemModel');
                        $values = $this->ConfigItemModel->getValueOptions($this->input->get('config'));
                        if ($values)
                        {
                            $new_attribute_values = explode(';',$values->value_options);
                        }
                    }

                    if ($this->input->get('selection'))
                    {
                        $selected = $this->input->get('selection');
                        $this->load->view('kiosk/batchajax',array('selections'=>$selections,'id'=>$kiosk_ids,'new_attribute_values'=>$new_attribute_values,'select'=>$selected));
                    }
                }
            }

            return false;
        }
    }
    
    /*
     * Function to unqueue selected configurations
     */
    public function unQueueKiosks()
    {
        $this->load->model('Kioskmodel');
        $is_ajax =  $this->input->get('rc');

        if ($this->input->get('kiosks'))
        {
            $kiosks_unqueued = json_decode($this->input->get('kiosks'));
            $unqueued = $this->Kioskmodel->unqueueKiosks($kiosks_unqueued);

            if ($unqueued)
            {
                if ($this->input->get('conds'))
                {
                    $conds = json_decode($this->input->get('conds'));
                    $data['selections'] = $this->Kioskmodel->getKioskByCondition($conds);

                    if ($this->input->get('config'))
                    {
                        $this->load->model('ConfigItemModel');
                        $values = $this->ConfigItemModel->getValueOptions($this->input->get('config'));
                        
                        if ($values)
                        {
                            $data['new_attribute_values'] = explode(';',$values->value_options);
                        }
                    }
                    if($is_ajax)
                    {
                        $msg = new POW\Classes\Message;
                        $msg->ok();
                        $msg->send();
                    }
                    else
                    {
                        $this->load->view('kiosk/batchajax',$data);
                    }

                }
            }

            return false;
        }
    }

    public function singlecommit()
    {
        $this->load->model('Kioskmodel');

        $datetime =  $this->input->post('datetime');
        $queued_id =  $this->input->post('queued_id');
        $current_id =  $this->input->post('current_id');

        $committed_res = $this->Kioskmodel->commitFromPick($queued_id, $current_id, $datetime);

        if ($committed_res>0)
        {
            $response = array('status' => true, 'result' => true,  'message' => "Successful");
        }
        else
        {
            $response = array('status' => false, 'result' => false,  'message' => "Error");
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }


    /*
    * function to commit queued kiosks
    */
    public function commitKiosks()
    {
        if ($this->input->post('selected'))
        {
            $this->load->helper('utility');
            $this->load->model('Kioskmodel');

            $committed = json_decode($this->input->post('selected'));
            $datetime =  $this->input->post('date_updated');
            $is_ajax =  $this->input->post('rc');

            if(!POW\Helpers\validate_date($datetime))
            {
                $datetime = date('Y-m-d H:i:s');
            }

            if ($this->input->post('date_unapplied'))
            {
                $this->Kioskmodel->commitSelectedKiosks($committed,$this->input->post('date_unapplied'), $datetime);
            }
        }
        else 
        {
            echo "No data";
        }
    }

    /*
     * Function to get Configuration History based on filter values
     *
     */
    public function getConfigHistory()
    {
        $this->load->model('Kioskmodel');

        if ($this->input->post('conditions'))
        {
            $conditions  = $this->input->post('conditions');
            $params = array();

            parse_str($conditions, $params);

            $data['history'] = $this->Kioskmodel->getHistory($params);

            $this->load->view('configitem/historyAjax',$data);
        }
    }
 

    public function getConfigsByKioskId()
    {
        $this->load->model('Kioskmodel');

        if ($this->input->get('kiosk_select'))
        {
            $kiosk_select = $this->input->get('kiosk_select');
            $data['kiosk'] = $this->Kioskmodel->getKioskById($kiosk_select);

            $data['kioskValues'] = $this->Kioskmodel->getAllKioskConfigurationOptions();
            $data['config_items']= $this->Kioskmodel->getModelConfigs($data['kiosk']->kiosk_model_id);
            $data['kiosk_config_items'] = $this->Kioskmodel->getKioskConfigs($kiosk_select);
            $data['kiosk_multiple_config_items'] = $this->Kioskmodel->getKioskMultipleConfigs($kiosk_select);

            if ($data['kiosk_config_items'])
            {
                $this->load->view('kiosk/kioskConfig',$data);
            }
        }

    }


}
