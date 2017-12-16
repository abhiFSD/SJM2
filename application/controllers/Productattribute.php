<?php

class Productattribute extends MY_Controller
{

    public function __construct()
    {
            parent::__construct();
            $this->acl->hasAccess();
            // loading Productattributemodel
            $this->load->model('Productattributemodel');
    }

    public function all()
    {


         if($this->session->userdata('email_address') == "" ) {
                 redirect('/user/login');
         }

         $this->load->model('Productattributemodel');

         $attributes = $this->Productattributemodel->getAllWithOptions();

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

        if ($this->input->post('save') == "Submit") {

                $unitData = array (

                );
             


                $data = array (
                                    "name"				=> $this->input->post('name'),
                            );

                if ($this->input->post('attribute_id') > 0 ) {
                        // update if already present
                        $productId = $this->input->post('attribute_id');
                        $this->db->update('item', $data, array('id' => $productId));	

                        $msg = "Item updated successfully";

                } else {
                        // enter new 
                        $this->db->insert('item', $data);
                        $msg = "New product created successfully";

                }
                $this->session->set_flashdata('msg', $msg);
                redirect('products/all');

        }

        $data['msg'] = $msg;

        // if id is present, start edit mode


        if ($id > 0 ) {
            $this->load->model('Productattributemodel');
            $data['attribute'] = $this->Productattributemodel->getById($id);
            $data['options']   = $this->Productattributemodel->getAttributeOptionValues($id);



        }else{
           $data['options'] = array();
        }



        $data['attributes_options'] = $this->Productattributemodel->getAllAtributesOptions(1);
        $data['units'] = $this->Productattributemodel->get_all_units();
        $data['attributes_charge_statues'] = $this->Productattributemodel->getAllAtributesOptions(7);

        $this->load->view("templates/header.php");
        $this->load->view('productattribute/manage', $data);
        $this->load->view("templates/footer.php");

   }
   
   /**
    * Delete the kiosk location
    * @param unknown $id
    */
   public function delete($id)
   {
   	if ($id > 0 ) {
   
   		$query = $this->db->get_where('item', array('id' => $id));
   		$results = $query->result();
   
   		if (count($results) > 0) {
   			 
   			$this->db->delete('item', array('id' => $id));
   			redirect('products/all');
   		} else {
   			redirect('404');
   		}
   
   	} else {
   		redirect('404');
   	}
   	 
   }
   
   /*
     * Function: save_product_attribute()
     * Description: This will save the product attribute as well as the product attribute options
     *
     * @param: POST
     * Return Value: array
     * @author: Avtar Gaur(developer.avtargaur@gmail.com)
     * Date: Dec 21, 2016
     */
    public function save_product_attribute(){


      //  print_r($this->input->post());

        $id= $this->input->post('attribute_id');

        if($id>0){

             $product_attribute_id = $this->Productattributemodel->updateAttribute($this->input->post());
             $this->Productattributemodel->remove_multiple_product_attribute_options($this->input->post('remove-option'));

             $this->Productattributemodel->save_multiple_product_attribute_options( $id);
        }else{

             // saving data to the product attribute table
             $product_attribute_id = $this->Productattributemodel->saveAttribute($this->input->post());

             $this->Productattributemodel->save_multiple_product_attribute_options( $product_attribute_id );
            
        }

        // saving data to the product attribute options table
      

        // returning json to add it in the attributes dropdown
        echo json_encode(array('id' => $product_attribute_id, 'name' => $this->input->post('attribute_name'), 'unit' => $this->input->post('unit_of_measure_id')));
    }

    /*
     * Function: get_product_attribute_options()
     * Description: This will get the product attribute options from the product_attribute_option table
     *
     * @param: POST
     * Return Value: array
     * @author: Avtar Gaur(developer.avtargaur@gmail.com)
     * Date: Dec 21, 2016
     */
    public function get_product_attribute_options(){
        // should work only in ajax
        if($this->input->is_ajax_request()){
            // getting options from the model
            $options = $this->Productattributemodel->getAttributeOptionValues($this->input->post('id'));

            // giving json response
            echo json_encode($options);
        }else{
          show_404();
        }
    }
}
