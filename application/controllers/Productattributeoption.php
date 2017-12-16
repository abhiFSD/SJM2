<?php

class Productattributeoption extends MY_Controller
{

    public function __construct()
    {
            parent::__construct();
            $this->acl->hasAccess();
    }

    public function all()
    {


         if($this->session->userdata('email_address') == "" ) {
                 redirect('/user/login');
         }

         $this->load->model('Productattributemodel');

         $attributes = $this->Productattributemodel->getAll();

         $msg = "";

         $this->load->view("templates/header.php");
         $this->load->view('productattribute/all', array('attributes' => $attributes,  'msg' => $msg));
         $this->load->view("templates/footer.php");

    } 


   
   
   /**
    * Manage the products
    * 
    * @param string $id
    */
   public function manage($id = null)
   {

        $msg = "";

        $this->load->model('Productattributemodel');
          $data = array ();
        if ($this->input->post('save') == "Submit") {

                $unitData = array (

                );

                $attribute_id = $this->input->post('attribute_id');
                $data = array (
                                    "name"				=> $this->input->post('name'),
                                    "sku_suffix"        => $this->input->post('sku_suffix'),
                                    "attribute_id"      => $attribute_id
                            );

                        // enter new
                $this->Productattributemodel->saveAttributeOption($data);
                $msg = "New product attribute option created successfully";

                $this->session->set_flashdata('msg', $msg);
                redirect('productattribute/manage/'. $attribute_id);

        }



        // if id is present, start edit mode
        if ($id > 0 ) {
            $data['attribute'] = $this->Productattributemodel->getById($id);
        } 


        $this->load->view("templates/header.php");
        $this->load->view('productattributeoption/manage', $data);
        $this->load->view("templates/footer.php");

   }
   
   /**
    * Delete the kiosk location
    * @param integer $id
    */
   public function delete($id)
   {
       $this->load->model('Productattributemodel');

       if ($id > 0 ) {
        $option = $this->Productattributemodel->getOptionById($id);
           if($option) {
                $this->db->delete('item_attribute_option', array('id' => $id));

                redirect('productattribute/manage/'. $option->product_attribute_id);

           }  else {
                redirect('404');
            }
   
        } else {
            redirect('404');
        }
   	 
   }
   

}
