<?php

class ProductModel extends CI_Model
{
    const TABLE = 'item';
    const SKU_TABLE = 'sku';

    /**
     *
     * Get the location based ont he ID
     *
     * @param integer $id
     * @return string
     */
    public function getById($id)
    {
        $query = $this->db->get_where(self::TABLE, array('id' => $id));

        if ($query->num_rows() > 0) 
        {
            return $query->row();
        } 

        return false;
    }

  /**
   * Get all the details based on the deployment
   *
   * @return array
   */
  public function getAll($criteria = array())
  {
    $this->db->select('p.id, s.name as product_name,ic.name as category_name, s.sku_value, p.name, p.sku, p.product_type,  p.description, s.ean13_barcode as barcode, p.status');
    $this->db->from(self::TABLE. ' as p');
    $this->db->join('sku as s', 'p.id = s.product_id');
    $this->db->join('item_category as ic', 'ic.product_category_id = p.product_category_id','left');
 
    $query = $this->db->get();

    $results = $query->result();
    return $results;
  }


   /**
   * Get all the details based on the deployment
   *
   * @return array
   */
  public function getAllEpm($criteria = array())
  {
    $this->db->select('p.id, p.name, p.sku,  p.description, s.ean13_barcode as barcode, p.status');
    $this->db->from(self::TABLE. ' as p');
    $this->db->join('sku as s', 'p.id = s.product_id');

    $this->db->where("product_type = 'epm' ");

    //if ($status != "") {
    //    $this->db->where("d.status = '".$status."'");
    // }
    $query = $this->db->get();

    $results = $query->result();

    return $results;
  }


  /**
   * Get all SKU By Product ID
   *
   * @return array
   */

  public function getSKUByProductId($id)
  {
    $query = $this->db->get_where('sku', array('product_id' => $id));

    if ($query->num_rows() > 0) {
      $data = $query->result_array();

      return $data;
    } else {

      return false;
    }
  }

    /**
     * Description: This will fetch all the products for batch offering change
     *
     * @return array
     */
    public function getAll_products_for_batch($criteria = array())
    {
        $query = $this->db
          ->select('p.id, p.name, p.sku, p.description, p.status')
          ->select('sku.name as sku_name')
          ->select('sku.sku_value')
          ->select('sku.id as sku_id')
          ->order_by('sku.sku_value', 'asc')
          ->order_by('sku.name', 'asc')
          ->from(self::TABLE. ' as p')
          ->join('sku', 'p.id = sku.product_id')
          ->get();

        $results = $query->result_array();

        return $results;
    }

  /*
     * Function: add_product()
     * Description: This will add a new product in the database.
     * Also save the other options in other tables as well using corressponding model and their method
     * @param NULL
     * Return Value: Array => Newly Inserted Row id
     *
     * @author: Avtar Gaur (developer.avtargaur@gmail.com)
     * Date: Dec 23, 2016
     */

    public function add_product() 
    {
        // inserting in the products table
        $this->db->insert(self::TABLE, [
            'name' => $this->input->post('name'),
            'sku' => $this->input->post('base_sku'),
            'description' => $this->input->post('description'),
            'status' => $this->input->post('status'),
            'name' => $this->input->post('name'),
            'product_type' => $this->input->post('product_type'),
            'date_created' => date('Y-m-d H:i:s'),
            'product_category_id' => $this->input->post('category'),
            'party_type_allocation_id' => $this->input->post('party_id'),
            'image_directory' => $this->input->post('image_directory'),
        ]);

        $product_id = $this->db->insert_id();

        $this->db->insert("item_category_classification", [
            'item_id' =>  $product_id,
            'item_category_id' =>$this->input->post('category'),
            'date_applied' => date('Y-m-d H:i:s'),
            'status' => $this->input->post('status')
        ]);

        // removing the image_directory from the current session
        $this->session->unset_userdata('product_directory');

        $images = $this->input->post('images');

        for($i = 0; $i < count($images); $i++)
        {
            $data = array(
                'location' => $images[$i],
                'product_id' => $product_id 
            );

            $this->db->insert('item_images', $data);
        }

        // creating new sku so that its unique
        $new_sku = $this->input->post('base_sku');

        // updating the sku column to be unique
        $data = array('sku' => $new_sku);
        $this->db->where('id', $product_id);
        $this->db->update(self::TABLE, $data);

        // saving the data to the sku table
        $data = array();

        $variant_attribute_ids = $this->input->post('variant_attribute_id');
        $variant_sku_suffixes = $this->input->post('variant_sku_suffix');
        $variant_attribute_option_ids = $this->input->post('variant_attribute_option_id');
        $variant_statuses = $this->input->post('variant_status');
        $variant_names = $this->input->post('variant_name');

        // inserting in the database with barcode
        if (count($variant_attribute_ids)) 
        {
            for ($i = 0; $i < count($variant_attribute_ids); $i++) 
            {
                $data = array(
                    'sku_value' => $new_sku . '-' . $variant_sku_suffixes[$i],
                    'name' => $this->input->post('name') . ' ' . $variant_names[$i],
                    'product_id' => $product_id,
                    'status' => $variant_statuses[$i],
                    'date_created' => date('Y-m-d H:i:s')
                );

                $this->updateSKU($data);
            }
        } 
        else 
        {
            $data = array(
                'sku_value' => $new_sku ,
                'name' => $this->input->post('name') ,
                'product_id' => $product_id,
                'status' => $this->input->post('status'), //@todo need to cross verify
                'date_created' => date('Y-m-d H:i:s')
            );

            $this->updateSKU($data);
        }

        // getting the attribute-option data from the post
        $attribute_options_data = $this->input->post('attribute-option');

        //only insert in the product_attribute_application table when there is any attribute option selected
        if(!empty($attribute_options_data))
        {
            // loading ProductattributeModel
            $this->load->model('Productattributemodel');

            // insert data in the product_attribute_application table
            $this->Productattributemodel->save_product_attribute_application($product_id, $attribute_options_data);
        }
    }

    private function updateSKU($data = array())
    {
        $this->db->insert(self::SKU_TABLE, $data);

        // id of the last inserted row in the sku table
        $sku_id = $this->db->insert_id();

        // creating number for barcode creation
        $number_string = str_pad($sku_id, 7, '0', STR_PAD_LEFT);
        $number = intval($number_string);

        // generating ean number
        $barcode = $this->generateEAN($number);

        // update SKU_TABLE with new barcode
        $this->db->where('id', $sku_id);
        $this->db->update(self::SKU_TABLE, array('ean13_barcode' => $barcode));
    }


    /*
     * Function: update_product()
     * Description: This will update a new product in the database.
     * Also save the other options in other tables as well using corressponding model and their method
     * @param NULL
     *  
     *
     * @author: Shamim
     *  
     */
    public function update_product() 
    {
        $product_id = $this->input->post('product_id');

        // inserting in the products table
        $this->db
            ->where('id', $product_id)
            ->update(self::TABLE, [
                'name' => $this->input->post('name'),
                'sku' => $this->input->post('base_sku'),
                'description' => $this->input->post('description'),
                'status' => $this->input->post('status'),
                'name' => $this->input->post('name'),
                'product_type' =>$this->input->post('product_type'),
                'date_created' => date('Y-m-d H:i:s'),
                'product_category_id' => $this->input->post('category'),
                'party_type_allocation_id' => $this->input->post('party_id')
            ]);

        $this->db->select('*');
        $this->db->from('item_category_classification');
        $this->db->where("item_id = '".$product_id."' and item_category_id=".$this->input->post('category'));
        $query = $this->db->get();

        $results = $query->result_array();
        if (count($results) == 0 )
        {
            $classification = array(
                'item_id' =>  $product_id,
                'item_category_id' =>$this->input->post('category'),
                'date_applied' => date('Y-m-d H:i:s'),
                'status' => $this->input->post('status')
            );

            $this->db->insert("item_category_classification", $classification);
        }

        // removing the image_directory from the current session
        // $this->session->unset_userdata('product_directory');

        $this->db->select('*');
        $this->db->from('item_category_classification');
        $this->db->where("item_id = '".$product_id."'");
        $query = $this->db->get();
        $results = $query->result_array();

        if (count($results) > 0 )
        {
            $this->db->delete("item_category_classification",  array('item_id' =>  $product_id  ));

            $this->db->insert("item_category_classification", [
                'item_id' =>  $product_id,
                'item_category_id' =>$this->input->post('category'),
                'date_applied' => date('Y-m-d H:i:s'),
                'status' => $this->input->post('status')
            ]);
        }

        $images = $this->input->post('images');

        for($i = 0; $i< count($images); $i++){
            $this->db->insert('item_images', [
                'location' => $images[$i],
                'product_id' => $product_id 
            ]);
        }

        $new_sku = $this->input->post('base_sku');

        // getting the attribute-option data from the post
        $attribute_options_data = $this->input->post('attribute-option');
        $variant_attribute_ids = $this->input->post('variant_attribute_id');
        $variant_sku_suffixes = $this->input->post('variant_sku_suffix');
        $variant_attribute_option_ids = $this->input->post('variant_attribute_option_id');
        $variant_statuses = $this->input->post('variant_status');
        $variant_names = $this->input->post('variant_name');

        for ($i = 0; $i< count($variant_attribute_ids); $i++)
        {
            $this->db->insert(self::SKU_TABLE, [
                'sku_value' => $new_sku.'-'.$variant_sku_suffixes[$i],
                'name' => $this->input->post('name').' '.$variant_names[$i],
                'product_id' => $product_id,
                'status' => $variant_statuses[$i],
                'date_created' => date('Y-m-d H:i:s')
            ]);
        }
      
        //only insert in the product_attribute_application table when there is any attribute option selected
        if (!empty($attribute_options_data))
        {
            // loading ProductattributeModel
            $this->load->model('Productattributemodel');

            $this->Productattributemodel->remove_previous_product_attribute_application($product_id);

            // insert data in the product_attribute_application table
            $this->Productattributemodel->save_product_attribute_application($product_id, $attribute_options_data);
        }
    }

    /*
     * Function: generateEAN()
     * Description: function for generating valid EAN 13 code
     *
     * @param $number
     * Return Value: $code
     *
     * @author: Avtar Gaur (developer.avtargaur@gmail.com)
     * Date: Dec 23, 2016
     */
   function generateEAN($number){
      $code = '20079' . str_pad($number, 7, '0', STR_PAD_LEFT);
      $weightflag = true;
      $sum = 0;
      // Weight for a digit in the check value is 3, 1, 3.. starting from the last digit.
      // loop backwards to make the loop length-agnostic. The same basic functionality
      // will work for codes of different lengths.
      for ($i = strlen($code) - 1; $i >= 0; $i--){
        $sum += (int)$code[$i] * ($weightflag?3:1);
        $weightflag = !$weightflag;
      }
      $code .= (10 - ($sum % 10)) % 10;
      return $code;
   }

   /*
     * Function: get_product_from_sku()
     * Description: function for getting products from the database for the SKU id entered
     *
     * @param $sku
     * Return Value: Products in array
     *
     * @author: Avtar Gaur (developer.avtargaur@gmail.com)
     * Date: Dec 26, 2016
     */
   function get_product_from_sku($sku = 0){
        // returning products from the product table
        return $this->db->get_where(self::TABLE, array('sku' => $sku))->result_array();
   }
 
   public function getSkuIdfromProductId($productId)
   {
      // $this->db->_compile_select();
       $this->db->select('p.id, p.name, p.sku, p.description, s.id as squid, s.ean13_barcode as barcode, p.status');
       $this->db->from(self::TABLE. ' as p');
       $this->db->join('sku as s', 'p.id = s.product_id');
       $this->db->where('p.id= '. $productId);
       $query = $this->db->get();
      //$this->db->last_query();
       if ($query->num_rows() > 0) {
           $results = $query->result_array();
           return $results;

       } else {
           return false;
       }

   }

   public function getAllActiveProducts($name)
   {
    
    $this->db->select('s.name, s.sku_value,s.id,p.id as productId');
    $this->db->from(self::SKU_TABLE . ' as s');
    $this->db->join(self::TABLE.' as p', 'p.id = s.product_id');
    $this->db->where('s.status','Active');
    $this->db->like('s.name',$name);
    //if ($status != "") {
    //    $this->db->where("d.status = '".$status."'");
    // }
    $query = $this->db->get();

    $results = $query->result();

    return $results;

   }

   public function getImagesByProductId($id)
    {
        $query = $this->db->get_where('item_images', array('product_id' => $id));

        if ($query->num_rows() > 0) {
            $data = $query->result();

            
            return $data;
        } else {

            return false;
        }
    }

}
