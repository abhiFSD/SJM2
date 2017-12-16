<?php if (!defined('BASEPATH')) {
    die();
}

class User extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->acl->hasAccess();
    }

    public function index()
    {
        redirect('stockmovement/jobs');
    }

    public function all()
    {
        $role = $this->session->userdata('role_id');
        $userId = $this->session->userdata('user_id');
        $centerId = $this->session->userdata('center_id');
        $users = POW\Party::employee_list();
        
        $this->load->view('templates/header',
            array('role' => $this->session->userdata('role_id'), 'name' => $this->session->userdata('name')));
        $this->load->view('user/list', array('users' => $users));
        $this->load->view('templates/footer');
    }

    public function edit($party_id = 0, $id = 0)
    {
        $msg = $this->session->flashdata('edit_user');

        if ($this->input->post('action') == "Save") 
        {
            $this->form_validation->set_rules('display_name', 'Display Name', 'required');
            $this->form_validation->set_rules('email_address', 'Email Address', 'required');
            $this->form_validation->set_rules('role_id', 'User Role', 'required');

            if ($this->form_validation->run()) 
            {
                $party_data = array(
                    'display_name' => $this->input->post('display_name'),
                    'email' => $this->input->post('email_address'),
                    'user_role_id' => $this->input->post('role'),
                    'status' => $this->input->post('party_status') == 1 ? 'Active' : 'Inactive',
                );

                if (!empty($party_id)) {
                    $party = POW\Party::with_id($party_id);
                    $party->assign_array($party_data);
                    $party->save();
                } 
                else 
                {
                    $party = new POW\Party();
                    $party->assign_array($party_data);
                    $party->save();

                    $party_type_allocation = new POW\PartyTypeAllocation();
                    $party_type_allocation->assign_array([
                        'party_id' => $party->id,
                        'party_type_id' => 6,
                        'user_role_id' => $this->input->post('role_id'),
                        'active' => 1,
                    ]);
                    $party_type_allocation->save();
                }

                $user_data = [
                    'email_address' => $this->input->post('email_address'),
                    'display_name' => $this->input->post('display_name'),
                    'role_id' => $this->input->post('role_id'),
                    'party_id' => $party->id,
                    'active' => $this->input->post('party_status'),
                    'inventory_location_id' => $this->input->post('inventory_location_id')
                ];

                $password = $this->input->post('password');
                $password = trim($password);
                if (empty($party_id) && empty($user_id)) 
                {
                    if (!empty($password)) {
                        $user_data['password'] = md5($this->input->post('password'));
                    } else {
                        $this->session->set_flashdata('edit_user', 'Password is empty.');

                        redirect('user/edit/' . $party->id . '/' . $id);
                    }
                } 
                elseif (!empty($password)) 
                {
                    $user_data['password'] = md5($password);
                }

                $user = POW\User::with_id($id);
                $user->assign_array($user_data);
                $user->save();

                $this->session->set_flashdata('success_message', 'User details updated successfully.');

                redirect('user/all');
            }
        }

        $role = $this->session->userdata('role_id');
        $userId = $this->session->userdata('user_id');
        $locations = POW\InventoryLocation::get_all(1);
        $userId = $this->session->userdata('user_id');
        $user = POW\User::with_id($id);
        $party = POW\Party::employee_with_id($party_id);

        $data['action'] = (!empty($party_id) || !empty($id)) ? 'Edit' : 'Add';
        $data['user'] = $user;
        $data['party'] = $party;
        $data['msg'] = $msg;
        $data['password'] = md5('powerpod' . time());
        $data['role_id'] = empty($party->role_id) ? $user->role_id : $party->role_id;
        $data['inventory_location_id'] = !empty($user->inventory_location_id) ? $user->inventory_location_id : "";
        $data['party_status'] = empty($party->status) ? $user->active : ('Active' == $party->status ? 1 : 0);
        $data['locations'] = $locations;

        $this->load->view("templates/header.php");
        $this->load->view('user/manage', $data);
        $this->load->view("templates/footer.php");
    }
}
