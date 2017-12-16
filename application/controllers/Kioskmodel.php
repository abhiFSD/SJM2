<?php

class Kioskmodel extends MY_Controller
{
    public function all()
    {
        $this->load->library('pagination');

        $this->load->Model('Kioskmodel_model');
        $kiosk = $this->Kioskmodel_model->getAll();

        $msg = "";

        $this->load->view("templates/header.php");
        $this->load->view('kioskmodel/all', array('kioskModels' => $kiosk, 'msg' => $msg));
        $this->load->view("templates/footer.php");

    }

    public function manage($id = null)
    {
        $msg = "";
        $this->load->Model('Kioskmodel_model');
        if ($this->input->post('save') == "Submit") {
            if ($this->input->post('kiosk_model_id') > 0) {
                //updating the model if ID present
                $kioskID = $this->input->post('kiosk_model_id');
                $this->Kioskmodel_model->editModel($kioskID);
                $msg = "Kiosk Model has been updated sucessfully";
                $this->session->set_flashdata('msg', $msg);
                redirect('kioskmodel/all');

            } else {
                $this->Kioskmodel_model->addModel();
                $msg = "Kiosk Model has been added successfully";
                $this->session->set_flashdata('msg', $msg);
                redirect('kioskmodel/all');
            }

        }
        if ($id > 0) {
            $kioskModel = $this->Kioskmodel_model->getOneById($id);
            if ($kioskModel) {
                $data['kioskModel'] = $kioskModel;
            }
            $kioskConfigs = $this->Kioskmodel_model->getKioskConfig($id); // getting the configuration details From config_item table based on the kiosk_model_id
            if ($kioskConfigs) {
                $data['kioskConfigs'] = $kioskConfigs;
            }
        }
        $data['msg'] = $msg;

        $this->load->view("templates/header.php");
        $this->load->view("kioskmodel/manage", $data);
        $this->load->view("templates/footer.php");
    }
}
