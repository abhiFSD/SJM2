<?php

class Productcategory extends MY_Controller
{

    public function __construct()
    {
            parent::__construct();
            $this->acl->hasAccess();

            // loading the Productcategorymodel
            $this->load->model('Productcategorymodel');
    }

    public function all()
    {


        if($this->session->userdata('email_address') == "" ) {
                 redirect('/user/login');
        }

        $this->load->model('Productcategorymodel');

        $categories = $this->Productcategorymodel->getAll();

      

        foreach ( $categories as $key => $c) {
         $a =array();

         $attributes = $this->Productcategorymodel->getItemCategoryAttributes($c->product_category_id);
         //$attributes = $this->Productcategorymodel->getItemCategoryAttributes($c->product_category_id);
        // getAttributeCategory
        
         if(count($attributes) >0 && $attributes)  {
                foreach($attributes as $key1 => $attribute) {
                    $a[] = $attribute['name'];    
               }
          }
            
           $categories[$key]->attributes =  implode(', ', $a);
        }
         
        //getItemCategoryAttributes

      
       
        $msg = "";

        $this->load->view("templates/header.php");
        $this->load->view('productcategory/all', array('categories' => $categories,  'msg' => $msg));
        $this->load->view("templates/footer.php");

    } 


       public function getattribute(){
          $a =array();
          $attributes = $this->Productcategorymodel->getItemCategoryAttributes($this->input->get('id'));
          echo json_encode($attributes);
   }
   
   
    /**
     * Manage the products
     * 
     * @param string $id
     */
    public function manage($id = null)
    {
      $msg = "";

      $this->load->model('Productcategorymodel');

      if ($this->input->post('save') == "Submit") 
      {
        $parent_id = $this->input->post('parent_id');
        $data = array (
          "name"				=> $this->input->post('name'),
          "parent_id"      => $parent_id,
          "item_category_type" => $this->input->post('item_category_type'),
        );

        if ($this->input->post('category_id')) {
          $data['id'] = $this->input->post('category_id');
        } 
        // enter new
        $cat_id  =    $this->Productcategorymodel->saveCategory($data);

        if($id>0)
        {
          $cat_id =  $id;
        }

        $attribute_ids = $this->input->post('attribute_ids');
        $isAt =   $this->Productcategorymodel->CheckAttributeCategory( $cat_id);
        if(count($attribute_ids)>0){
          foreach ($attribute_ids as $key => $attribute) {
            $data2 = array (
              "item_cateogry_id"              =>  $cat_id, 
              "item_attribute_id"      => $attribute
            );
            $at =   $this->Productcategorymodel->saveCategoryAttribute($data2);  
          }
        }

        $msg = "Category saved successfully";

        $this->session->set_flashdata('msg', $msg);
        redirect('productcategory/all');
      }

      // if id is present, start edit mode
      if ($id > 0 ) {
        $data['category'] = $this->Productcategorymodel->getById($id);
        $attributes = $this->Productcategorymodel->getItemCategoryAttributes($id);

        $a = array();
        if( count($attributes) >0 && $attributes)  {
          foreach($attributes as $key1 => $attribute) {
            $a[$attribute['id']] = $attribute['name'];    
          }
        }

        $data['attrs']  = $a;
      }

      $this->load->model('Productattributemodel');

      $data['attributes'] = $this->Productattributemodel->getAll();
      $data['categories'] = $this->Productcategorymodel->getAll();

      $this->load->view("templates/header.php");
      $this->load->view('productcategory/manage', $data);
      $this->load->view("templates/footer.php");
    }

    /*
     * Function: add_new_product_category()
     * Description: add a new product category via Ajax
     * 
     * @param: $_POST
     * @return: inserted id
     * @author: Avtar Gaur (developer.avtargaur@gmail.com)
     * Date: Dec 17, 2016
     */
   public function add_new_product_category(){
        // works only if the request is of ajax type
        if($this->input->is_ajax_request()){
            // inserting into the products category table
            $inserted_id = $this->Productcategorymodel->saveCategory($this->input->post());

            // showing json as response
            echo json_encode(array("inserted_id" => $inserted_id));
        }
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
