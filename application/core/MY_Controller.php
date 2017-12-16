<?php

class MY_Controller extends CI_Controller
{
    protected $view_data = [];

    function __construct()
    {
        parent::__construct();

        $this->migration->current();

        $this->check_session();
    }

    protected function default_view($view) 
    {
        $this->load->view('templates/header', $this->view_data);
        $this->load->view($view, $this->view_data);
        $this->load->view('templates/footer', $this->view_data);
    }

    protected function get_upload($form_input_name)
    {
        $config['upload_path']          = './uploads/';
        $config['allowed_types']        = 'csv';
        $config['max_size']             = 2048;
        
        $this->load->library('upload', $config);

        return $this->upload->do_upload($form_input_name) ? $this->upload->data() : '';
    }

    protected function json_output($data)
    {
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($data));
    }

    private function check_session()
    {
        if ($this->uri->segment(1) == 'auth') return true;

        if (!$this->session->userdata('email_address')) {
            redirect('/auth/login');
        }
    }

}
