<?php

class Products extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->acl->hasAccess();

        // loading the products category model
        $this->load->model('Productcategorymodel');

        // loading the products model
        $this->load->model('Productmodel');
    }

    public function all()
    {
        if($this->session->userdata('email_address') == "" ) {
            redirect('/user/login');
        }

        $this->load->model('Productmodel');

        $products = $this->Productmodel->getAll();

        $msg = "";

        $this->load->view("templates/header.php");
        $this->load->view('products/all', array('products' => $products,  'msg' => $msg));
        $this->load->view("templates/footer.php");
    }

    /*
     * Function: manage()
     * Description: This will add a new product in the database.
     * Also save the other options in other tables as well using corressponding model and their method
     * @param $id
     * Return Value: Array => Newly Inserted Row id
     *
     * @author: Avtar Gaur (developer.avtargaur@gmail.com)
     * Date: Dec 23, 2016
     */
    public function manage($id = null)
    {
        $msg = "";

        // saving the data to the database
        if ($this->input->post('save') == "Submit") 
        {
            if($this->input->post('product_id') > 0 )
            {
                // update if already present
                $productId = $this->input->post('product_id');
                $this->Productmodel->update_product($productId);

                $msg = "Product has been updated successfully";
            }
            else
            {
                // insert a new product in the database
                $this->Productmodel->add_product();
                $msg = "New product has been added successfully.";
            }

            $this->session->set_flashdata('msg', $msg);
            redirect('products/all');
        }

        $data['msg'] = $msg;

        // if id is present, start edit mode
        if ($id > 0 ) 
        {
            // fetching product from the database
            $data['product'] = $product = $this->Productmodel->getById($id);
            $data['allsku']  = $this->Productmodel->getSKUByProductId($id);
            $data['images']  = $this->Productmodel->getImagesByProductId($id);

            // checking if the product actually exists
            if(!empty($product))
            {
                // only if there is a directory already present for this product images
                if(!empty($product->image_directory))
                    // setting the upload directory name in the session
                    $this->session->set_userdata('product_directory', $product->image_directory);
                else
                    // setting the upload directory name in the session
                    $this->session->set_userdata('product_directory', $this->session->userdata('__ci_last_regenerate').'-dir-'.strtotime("now"));
            }
            else
            {
                // if product does not exists then we redirect the user to the 404 page
                show_404();
            }
        }
        else
        {
            $data['product'] = false;
            $data['allsku'] = [];
            $data['images'] = [];

            if(!$this->session->userdata('product_directory'))
            {
                // setting the product_directory name in the session so that it can be used for uploads and fetching downloads
                $this->session->set_userdata('product_directory', $this->session->userdata('__ci_last_regenerate').'-dir-'.strtotime("now"));
            }
        }

        $this->load->model('Partymodel');
        $this->load->model('ProductattributeModel');

        // all product categories
        $data['product_categories'] = $this->Productcategorymodel->get_product_categories(false);

        // get all parties/product owners
        $data['parties'] = $this->Partymodel->getProductOwners();

        // get all the units from the product_attribute tables
        $data['units'] = $this->ProductattributeModel->get_all_units();

        // getting all the product attributes
        $data['attributes'] = $this->ProductattributeModel->getAll();
        $data['attributes_options'] = $this->ProductattributeModel->getAllAtributesOptions(1);
        $data['attributes_charge_statues'] = $this->ProductattributeModel->getAllAtributesOptions(7);
        $data['attributes_options_selected'] = $this->ProductattributeModel->getAllAtributesOptionsSelected($id);

        if(!isset($data['attributes_options_selected']))
        $data['attributes_options_selected'] = array();

        $this->load->view("templates/header.php");
        $this->load->view('products/manage_new', $data);
        $this->load->view("templates/footer.php");
    }

   /*
     * Function: uploads()
     * Description: This will Upload the images for the Product
     *
     * @param: NULL
     * Return Value: Array => All the images for this product
     *
     * @author: Avtar Gaur (developer.avtargaur@gmail.com)
     * Date: Dec 18, 2016
     */

    public function uploads() {
      error_reporting(E_ALL | E_STRICT);
      $result=  $this->load->library("UploadHandler");

    }

    /*
     * Function: check_for_sku()
     * Description: This will check for the uniqueness of "SKU" value in the Products table
     * Will work only with the Ajax call
     *
     * @param $_POST
     * Return Value: json for result
     *
     * @author: Avtar Gaur (developer.avtargaur@gmail.com)
     * Date: Dec 26, 2016
     */
   public function check_for_sku(){
      // should work only for ajax call
      if($this->input->is_ajax_request()){
         // fetch product from the database
         $products = $this->Productmodel->get_product_from_sku($this->input->post('sku'));

         // if product exists them return true else false
         if(count($products))
            echo json_encode(array('result'=> 'true'));
          else
            echo json_encode(array('result'=> 'false'));
      }else{
        // otherwise show the error 404 page
        show_404();
      }
   }



   /**
    * Delete the kiosk location
    * @param unknown $id
    */
   public function deleteimage()
   {

    $id = abs($this->input->post('id'));


    if ($id > 0 ) {

      $query = $this->db->get_where('item_images', array('id' => $id));
      $results = $query->result();

      if (count($results) > 0) {

        $this->db->delete('item_images', array('id' => $id));

         echo json_encode(array('result'=> 'true'));
      } else {
         echo json_encode(array('result'=> 'false'));
      }

    } else {
      echo json_encode(array('result'=> 'false'));
    }

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

   public function assignskunew()
   {
        $options = new stdClass();
        $options->item_category_type = ['product'];

        $data['warehouses'] = POW\InventoryLocation::list_key_val('id', 'name', [['name', 'asc']]);

        if ($this->input->post())
        {
            $options->item_category_type = $this->input->post('item_category_type');

            $data['items'] = POW\Sku::get_all($options);

            return $this->load->view('products/sections/table', $data);
        }

        $data['items'] = POW\Sku::get_all();

        $this->load->view("templates/header.php");
        $this->load->view('products/assignskunew', $data);
        $this->load->view("templates/footer.php");
   }

   public function saveassignment($item, $location, $action)
   {

       try {
           if ($action == 'false') {
               $this->db->where('sku_id', $item);
               $this->db->where('location_id', $location);
               $this->db->delete('inventory_item');
           } else {
               $insertData = array('sku_id' => $item, 'location_id' => $location, 'SOH' => 0);
               $this->db->insert('inventory_item', $insertData);
           }
       } catch (Exception $e) {
           echo "Unable to update the record. " . $e->getMessage();

       }

       echo "SKUs assigned successfully";

   }

    public function getActiveProducts()
    {

      if($this->input->get('term')){
        $name = $this->input->get('term');
        $this->load->model('Productmodel');

        $products = $this->Productmodel->getAllActiveProducts($name);
        
        echo (json_encode($products));
      }

    }

}
