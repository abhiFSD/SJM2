<?php

class ProductcategoryModel extends CI_Model
{

    const TABLE = 'item_category';

    /**
     *
     * Get the category based ont he ID
     *
     * @param integer $id
     * @return string
     */
    public function getById($id)
    {
        $query = $this->db->get_where(self::TABLE, array('product_category_id' => $id));

        if ($query->num_rows() > 0) {
            $data = $query->result();

            return $data[0];
        } else {

            return false;
        }
    }

    public function CheckAttributeCategory( $cid)
    {
        $query =$this->db->delete('item_category_default_attributes', array('item_cateogry_id'=>$cid));
        //  return $query->num_rows();
         
    }

    public function getAttributeCategory( $cid)
    {
        $query = $this->db->get_where('item_category_default_attributes', array('item_cateogry_id'=>$cid));
        
        if ($query->num_rows() > 0) {
          $data = $query->result_array();

          return $data;
        } else {

          return false;
        }
         
    }

  public function getItemCategoryAttributes($cid){

    $query = $this->db->query("SELECT item_attribute.name, item_attribute.id, item_attribute.unit_of_measure_id FROM `item_category_default_attributes` left JOIN  item_attribute on  item_attribute.id=`item_category_default_attributes`.item_attribute_id WHERE item_category_default_attributes.item_cateogry_id=$cid");
    
    if ($query->num_rows() > 0) {
      $data = $query->result_array();

      return $data;
    } else {
      return false;
    }

  }

    public function getAllArray()
    {
        $query = $this->db->get('item_category');

        $results = $query->result_array();

        return $results;
    }


    /**
     * Get all the details
     *
     * @return array
     */
    public function getAll()
    {
        $query = $this->db->get('item_category');

        $results = $query->result();

        return $results;
    }

/**
     * Save category attribute
     *
     * @param $params
     */
    public function saveCategoryAttribute($data)
    {

         
        $this->db->insert("item_category_default_attributes", $data);
         


        return $this->db->insert_id();
    }
    /**
     * Save category option values
     *
     * @param $params
     */
    public function saveCategory($params)
    {
        if(!isset($params['status']))
        $params['status'] = 1;

        $data = array(
            'name' => $params['name'],
            'parent_product_category_id' => $params['parent_id'],
            'status' => $params['status'],
            'item_category_type' => $params['item_category_type'],
        );

        if (!isset($params['id'])) {
            $this->db->insert(self::TABLE, $data);

            // returning the last inserted id
            return $this->db->insert_id();
        } else {
            $this->db->update(self::TABLE, $data, array('product_category_id' => $params['id']));
        }

        return true;
    }
    
    /*
     * Function: get_product_categories()
     * Description: gets all the active product categories
     * Return Value: Array => ("field" => "value")
     *
     * @author: Avtar Gaur (developer.avtargaur@gmail.com)
     * Date: Dec 17, 2016
     */
    public function get_product_categories($products_only = true)
    {
        if ($products_only)
        {
            $this->db
                ->where('parent_product_category_id !=', 0)
                ->where('item_category_type', 'product');
        }

        $query = $this->db
            ->where('status', 1)
            ->where('product_category_id not in (SELECT parent_product_category_id FROM `item_category` WHERE `status` = 1 AND parent_product_category_id <> product_category_id AND parent_product_category_id > 0 AND parent_product_category_id IS NOT NULL)', null, false)
            ->order_by('item_category.name', 'asc')
            ->get('item_category');
 
        $result = $query->result_array(); 
        return $result;
    }

}
