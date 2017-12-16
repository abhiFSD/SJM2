<?php if (!defined('BASEPATH')) die();
class Auth extends CI_Controller 
{
    /**
     *  perform the login
     */
    public function login()
    {
        $error = "";

        if ($this->input->post('do') == "Login") 
        {
            $this->form_validation->set_rules('username', 'Username', 'trim|required|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'required');
            
            if ($this->form_validation->run()) {
                // do the login check
                $result = $this->doLogin($this->input->post('username'),  $this->input->post('password'));
                if ($result) {
                    redirect('/stockmovement/jobs');
                } else {
                    $error = "Invalid Username/Password. Please try again";
                }
            }
        }
        
        $data['disableMenu']= true;

        $this->load->view('templates/header', $data);
        $this->load->view('auth/login', array('error' => $error));
        $this->load->view('templates/footer');
    }
    
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('/auth/login');
    }
        
    private function doLogin($email, $password)
    {
        $query = $this->db->get_where('user', array('email_address' => $email, 'password' => md5($password)));
        
        if ($query->num_rows() > 0) 
        {
            $row = $query->row(0);
            $data = array (
                "user_id"           => $row->id,
                "email_address"     => $row->email_address,
                "role_id"           => $row->role_id ? $row->role_id : 4,       
                "name"              => $row->display_name
            );
            $this->load->library('session');
            $this->session->set_userdata($data);
            return true;
        } else {
            return false;
        }
    }

}
