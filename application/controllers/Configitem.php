<?php

class Configitem extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();


    }

    public function all()
    {

        $filter_conds = array();
        $this->load->library('pagination');

        if ($this->session->userdata('email_address') == "") {
            redirect('/user/login');
        }
        //to get values of apply to for drop down box
        $this->db->distinct();
        $this->db->select('apply_to');
        $this->db->from('config_item');
        $applyto_filters = $this->db->get();

        //  to get values of uom for drop down box
        ////$this->db->distinct();
        //$this->db->select('id,name');
        //$this->db->from('unit_of_measure');
        //$uom_filters= $this->db->get();

        $this->db->distinct();
        $this->db->select('unit_of_measure');
        $this->db->from('config_item');
        $where = 'unit_of_measure is NOT NULL AND unit_of_measure <> ""';
        $this->db->where($where);
        $uom_filters = $this->db->get();

        //to get values of field type for drop down box
        $this->db->distinct();
        $this->db->select('field_type');
        $this->db->from('config_item');
        $type_filters = $this->db->get();

        $this->db->select('c.id,c.name,c.field_type,c.category_id,cc.name as category_name,c.value_options,c.apply_to,c.status,c.unit_of_measure as uom');
        $this->db->from('config_item as c');
        $this->db->join('config_item_category as cc','cc.id = c.category_id', "left");

        if ($this->input->get('apply_to')) {
            $applyto = urldecode($this->input->get('apply_to'));
            $filter_conds['apply_to'] = $applyto;
            if ($this->input->get('field_type')) {
                $fieldtype = urldecode($this->input->get('field_type'));
                $filter_conds['field_type'] = $fieldtype;
                if ($this->input->get('uom')) {
                    $uom = urldecode($this->input->get('uom'));
                    $filter_conds['uom'] = $uom;
                    if ($this->input->get('status')) {
                        $status = urldecode($this->input->get('status'));
                        $filter_conds['status'] = $status;
                        $cond1 = array("apply_to" => $applyto, "field_type" => $fieldtype, "unit_of_measure" => $uom, "status" => $status);
                        $this->db->where($cond1);
                    } else {
                        $this->db->where(array("apply_to" => $applyto, "field_type" => $fieldtype, "unit_of_measure" => $uom));
                    }
                } else if ($this->input->get('status')) {
                    $status = urldecode($this->input->get('status'));
                    $filter_conds['status'] = $status;
                    $cond2 = array("apply_to" => $applyto, "field_type" => $fieldtype, "status" => $status);
                    $this->db->where($cond2);
                } else {
                    $cond3 = array("apply_to" => $applyto, "field_type" => $fieldtype);
                    $this->db->where($cond3);
                }
            } else if ($this->input->get('uom')) {
                $uom = urldecode($this->input->get('uom'));
                $filter_conds['uom'] = $uom;
                if ($this->input->get('status')) {
                    $status = urldecode($this->input->get('status'));
                    $filter_conds['status'] = $status;
                    $this->db->where(array("apply_to" => $applyto, "unit_of_measure" => $uom, "status" => $status));
                } else {
                    $this->db->where(array("apply_to" => $applyto, "unit_of_measure" => $uom));
                }
            } else if ($this->input->get('status')) {
                $status = urldecode($this->input->get('status'));
                $filter_conds['status'] = $status;
                $this->db->where(array("apply_to" => $applyto, "status" => $status));
            } else {
                $this->db->where("apply_to", $applyto);
            }

        } else if ($this->input->get('field_type')) {
            $fieldtype = urldecode($this->input->get('field_type'));
            $filter_conds['field_type'] = $fieldtype;
            if ($this->input->get('uom')) {
                $uom = urldecode($this->input->get('uom'));
                $filter_conds['uom'] = $uom;
                if ($this->input->get('status')) {
                    $status = urldecode($this->input->get('status'));
                    $filter_conds['status'] = $status;
                    $this->db->where(array("field_type" => $fieldtype, "unit_of_measure" => $uom, "status" => $status));
                } else {
                    $this->db->where(array("field_type" => $fieldtype, "unit_of_measure" => $uom));
                }
            } else if ($this->input->get('status')) {
                $status = urldecode($this->input->get('status'));
                $filter_conds['status'] = $status;
                $this->db->where(array("field_type" => $fieldtype, "status" => $status));
            } else {
                $this->db->where('field_type', $fieldtype);
            }
        } else if ($this->input->get('uom')) {
            $uom = urldecode($this->input->get('uom'));
            $filter_conds['uom'] = $uom;
            if ($this->input->get('status')) {
                $status = urldecode($this->input->get('status'));
                $filter_conds['status'] = $status;
                $this->db->where(array("unit_of_measure" => $uom, "status" => $status));
            } else {
                $this->db->where("unit_of_measure", $uom);
            }
        } else if ($this->input->get('status')) {
            $status = urldecode($this->input->get('status'));
            $filter_conds['status'] = $status;
            $this->db->where('status', $status);
        }


        //  $filter_conds['apply_to']= $applyto;
        //  $this->db->where("apply_to",$applyto);
        // }


        //  if(isset($applies_to)){
        //    $this->db->where('c.apply_to',$applies_to);
        //  }
        //  if(isset($type)){
        //    $this->db->where('c.files_type',$type);
        //  }

        $query = $this->db->get();

        $msg = "";

        $this->load->view("templates/header.php");
        $this->load->view('configitem/all', array('configs' => $query, 'filters' => $filter_conds, 'applyto_filters' => $applyto_filters, 'uom_filters' => $uom_filters, 'type_filters' => $type_filters, 'msg' => $msg));
        $this->load->view("templates/footer.php");
    }

    public function manage($id = null)
    {
        $msg = "";
        //   //$data = array();
        //
        //

      //  print_r($this->input->post());
        if ($this->input->post('save') == "Submit") {
            $data = array(
                'name' => $this->input->post('name'),
                'field_type' => $this->input->post('fieldType'),
                'value_options' => $this->input->post('valueOptions'),
                'apply_to' => $this->input->post('applyTo'),
                'unit_of_measure' => $this->input->post('uom'),
                'status' => $this->input->post('status'),
                'category_id' => $this->input->post('category_id')



            );
            if ($this->input->post('configId') > 0) {
                $configId = $this->input->post('configId');
                $this->db->update('config_item', $data, array('id' => $configId));

                $msg = "Configuration Field updated successfully";
                $this->session->set_flashdata('msg', $msg);
                redirect('configitem/all');
            } else {
                // enter new
                $this->db->insert('config_item', $data);
                $this->session->set_flashdata('msg', 'New Configuration Field created successfully');
                redirect('configitem/all');
            }
        }
        $data['msg'] = $msg;

        if ($id > 0) {
            $this->db->select('c.id,c.name,c.field_type,c.category_id,c.value_options,c.unit_of_measure as uom,c.apply_to,c.status');
            $this->db->from('config_item as c');
            //  $this->db->join('unit_of_measure as u','c.unit_of_measure_id = u.id','left');
            $this->db->where('c.id', $id);
            $query = $this->db->get();
            $results = $query->result();
            if (count($results) > 0) {
                $configitem = $results[0];
                $data['configitem'] = $configitem;
            }
        }

        $this->db->distinct();
        $this->db->select('unit_of_measure');
        $this->db->from('config_item');
        $where = 'unit_of_measure is NOT NULL AND unit_of_measure <>""';
        $this->db->where($where);
        $data['measurementunits'] = $this->db->get();




        $this->db->from('config_item_category');


        $data['config_item_category'] = $this->db->get();


        $this->load->view("templates/header.php");
        $this->load->view('configitem/manage', $data);
        $this->load->view("templates/footer.php");

    }


    private function getnextId()
    {
        $query2 = $this->db->query("SHOW TABLE STATUS FROM powerpod_db WHERE name LIKE 'unit_of_measure' ");
        //$query2 = $this->db->query("SHOW TABLE STATUS FROM nowgrou_demo WHERE name LIKE 'unit_of_measure' ");
        $values = $query2->result();
        if (count($values) > 0) {
            $val = $values[0];
            return ($val->Auto_increment);
        } else {
            return 1;
        }
    }

    /**
     * function to change configurations in  batch
     *
     *
     *
     */

    public function batchChange()
    {
        $data = array();
        $this->load->model('Kioskmodel_model');
        $data['models'] = $this->Kioskmodel_model->getAll(); //To get all the kiosk models
        $this->load->model('ConfigitemModel');
        $data['states'] = $this->ConfigitemModel->getStates(); //To get all the states
        $data['kiosks'] = $this->ConfigitemModel->getAllKiosks();
        $data['site_categories'] = $this->ConfigitemModel->getSitesCategory();
        $data['configItems'] = $this->ConfigitemModel->getAll();

        $this->load->view("templates/header.php");
        $this->load->view('configitem/batchChange', $data);
        $this->load->view("templates/footer.php");
    }



    public function singleChange()
    {
        $data = array();
        $this->load->model('Kioskmodel_model');
        $data['models'] = $this->Kioskmodel_model->getAll(); //To get all the kiosk models
        $this->load->model('ConfigitemModel');
        $data['states'] = $this->ConfigitemModel->getStates(); //To get all the states
        $data['kiosks'] = $this->ConfigitemModel->getAllKiosks();
        $data['site_categories'] = $this->ConfigitemModel->getSitesCategory();
        $data['configItems'] = $this->ConfigitemModel->getAll();

        $this->load->view("templates/header.php");
        $this->load->view('configitem/singleChange', $data);
        $this->load->view("templates/footer.php");
    }


    public function processqueue()
    {
         $this->load->model('Kioskmodel');

        $attributes = $this->input->post();

        $datetime =  date('Y-m-d H:i:s');

        unset($attributes['change_kiosk']);
        unset($attributes['copy_kiosk']);
        unset($attributes['datetime']);

        $kiosk_id = $this->input->post('change_kiosk');
        $refresh = false;
        $data  = array();
        $err = "";
        foreach ($attributes['attribute']['new'] as $attributeId => $attribute)
        {
            if(isset($attributes['attribute']['current'][$attributeId]))
            {
                $current = $attributes['attribute']['current'][$attributeId];
            }else
                 $current =  "";

            if(isset($attributes['attribute']['queued'][$attributeId]))
            {
                $queued = $attributes['attribute']['queued'][$attributeId];
            }else
                 $queued =  "";
            /*
            Please remove the messages. The logic should work as follows:
            1. If Current Value and New Value are same and Queued value is not null, then delete queued value
            2. If Current Value and New Value are same and Queued value is null, then ignore New Value
            3. If Queued Value and New Value are same then ignore new value
            4. Otherwise, replace Queued Value with New Value
            */

            if(  ( $attribute!="") ){

               if($attribute ==$current && $queued !="")
               {
                    $queued = $this->Kioskmodel->deleteKioskConfig($kiosk_id,$attributeId);
                    $refresh  = true;

                }
                else if($attribute ==$current && $queued =="")
                {
                    $refresh  = true;
                }
               else if($attribute ==$queued )
               {
                 //   $err = "Queued value and new value can not be the same";
                     $refresh  = true;
                }
               else
                {
                     $data[] = array($attribute,$attributeId,$kiosk_id);
                }
            }
        }

        $data['datetime'] = $datetime;

        $queued = $this->Kioskmodel->queueKiosks($data);

        unset($data['datetime']);

        if(count($data)>0)
        {
            $response = array('status' => true, 'result' => true, 'info'=>$data, 'message' => "Successful", 'redirect'=>'/configitem/batchHistory?status=Queued&json='.base64_encode(json_encode($data)));

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
        }
        else if( $refresh && count($data)==0)
        {
            $response = array('status' => true,'refresh'=>$refresh, 'result' => true, 'info'=>$data, 'message' => "Successful", 'redirect'=>'/configitem/batchHistory?status=Queued&json='.base64_encode(json_encode($data)));

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
        }
        else
        {
            $response = array('status' => false,'refresh'=>$refresh, 'result' => false, 'info'=>$data, 'message' =>  $err);
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
          }
    }


    public function getValues()
    {
        if ($this->input->get('itemName')) {
            $item_name = $this->input->get('itemName');
            $v = $this->input->get('value');
            $this->load->model('ConfigitemModel');
            $values = $this->ConfigitemModel->getValueOptions($item_name);

            $selected = '';

            if ($values) {
                $new_attribute_values = explode(';', $values->value_options);
                // echo "<option value=''>Select</option>";
                foreach ($new_attribute_values as $value) {
                    $value = trim($value);
                    if($v== $value)
                    {
                        $selected = 'selected';
                    }else
                    {
                        $selected = '';
                    }
                    echo "<option value ='" . $value . "' $selected >" . $value . "</option>";
                }
            }
        }
    }

    public function batchHistory()
    {
        $data = array();
        $this->load->model('Kioskmodel_model');
        $this->load->model('Kioskmodel');
        $this->load->model('ConfigitemModel');

        $data['models'] = $this->Kioskmodel_model->getAll(); //To get all the kiosk models
        $data['kiosk_active'] = $this->Kioskmodel->getActivelyDeployedKiosks();
        $data['states'] = $this->ConfigitemModel->getStates(); //To get all the states
        $data['kiosks'] = $this->ConfigitemModel->getAllKiosks();
        $data['categories'] = $this->ConfigitemModel->getSitesCategory();
        $data['configItems'] = $this->ConfigitemModel->getAll();
        $data['status'] = $this->input->get('status');
        $data['config'] = $this->input->get('config');
        $data['value'] = $this->input->get('value');
        $data['json'] = $this->input->get('json');

        $today = new DateTime();
        $data['today_date'] = $today->format('Y-m-d')."T".$today->format('H:i:s').".000";

        if ($this->input->get('order')) 
        {
            $data['sort'] = $this->input->get('order');
        } 
        else 
        {
            $data['sort'] = 0;
        }

        $data['body_id'] = 'kiosk-attributes';
        $this->view_data = $data;
        $this->default_view('configitem/batchHistory');
    }

      public function getSingleConfigsByKioskId()
      {
        $this->load->model('Kioskmodel');
        $this->load->model('ConfigItemModel');


        if($this->input->get('kiosk_select')){
          $kiosk_select = $this->input->get('kiosk_select');
          $data['kiosk'] = $this->Kioskmodel->getKioskById($kiosk_select);
        // $data['kioskValues'] = $this->Kioskmodel->getAllKioskConfigurationOptions();
          $data['config_items']= $this->Kioskmodel->getModelConfigs($data['kiosk']->kiosk_model_id);
          $data['kiosk_config_items'] = $this->Kioskmodel->getKioskConfigs($kiosk_select);



          $data['kiosk_config_items_queued'] = $this->Kioskmodel->getKioskConfigsQueued($kiosk_select);

          //print_r($data['kiosk_config_items_queued'] );

          //getKioskConfigsQueued

       //   print_r( $data['kiosk_config_items']);
          $data['configItems'] = $this->ConfigItemModel->getAllWithTypes();



          if($data['kiosk_config_items']){
            $response = array('status' => true, 'result' => true, 'info'=>$data, 'message' => "Successful");
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
                // $this->load->view('configitem/singleAttribute',$data);
          }else
          {

            $response = array('status' => false, 'result' => false, 'info'=>$data, 'message' => "Error");
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));

          }


      }

      }


}
