<?php
class Uom extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
  }

  public function addNewUom()
  {
   $msg = "";
    if ($this->input->post('ajax')) {

       $data = array (
        "name"	=> $this->input->post('name')
               );

           $this->db->insert('unit_of_measure', $data);
         }

     $data['msg'] = $msg;

  }
}
