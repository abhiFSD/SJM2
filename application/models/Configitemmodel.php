<?php
class ConfigitemModel extends CI_Model
{
  const TABLE = 'config_item';
  const KIOSKTABLE = 'kiosk_config_item';
  /**
  * function to get all the states associated with kiosks
  * @return array of states
  *
  *
  */
  public function getStates()
  {
    $this->db->distinct();
    $this->db->select('state');
    $this->db->from('site');
    $this->db->order_by('state','asc');
    $query = $this->db->get();
    if($query->num_rows() > 0){
      return $query->result();
    } else{
      return false;
    }
  }
  /**
  * function to get all site categories
  * @return array
  *
  *
  */

  public function getSitesCategory()
  {
    $this->db->distinct();
    $this->db->select('category');
    $this->db->from('site');
    $this->db->order_by('category','asc');
    $query = $this->db->get();
    if($query->num_rows() > 0){
      return $query->result();
    } else{
      return false;
    }
  }

 /**
 * Function to get all active kiosk numbers
 * @return array
 *
 *
 */
 public function getAllKiosks()
 {
   $this->db->select('number,id');
   $this->db->from('kiosk');
   $this->db->where('status','Active');
   $query = $this->db->get();
   if($query->num_rows() > 0){
     return $query->result();
   } else{
     return false;
   }
 }

 public function getProductCategory()
 {
   $this->db->select('product_category_id,name');
   $this->db->from('item_category');
   $query = $this->db->get();
   if($query->num_rows() > 0){
     return $query->result();
   } else{
     return false;
   }

 }
 /**
 * function to get the id and name of all configuration items from config_item table
 * @return array
 *
 *
 */
 public function getAll()
 {
   $this->db->select('id,name');
   $this->db->from(self::TABLE);
   $this->db->where('apply_to','kiosk');

   $this->db->order_by("name", "asc");

   $query = $this->db->get();

   //print_r($this->db->last_query());

   if($query->num_rows() > 0){
     return $query->result();
   } else{
     return false;
   }
 }


/**
 * function to get the id and name of all configuration items from config_item table with field types
 * @return array
 *
 *
 */
 public function getAllWithTypes()
 {
   $this->db->select('id,name,field_type,value_options');
   $this->db->from(self::TABLE);
   $this->db->where('apply_to','kiosk');
   $this->db->order_by("name", "asc");
   $query = $this->db->get();
  // print_r($this->db->last_query());

   if($query->num_rows() > 0){
     return $query->result();
   } else{
     return false;
   }
 }
 /**
 *
 *
 *
 *
 */
//SELECT * FROM `kiosk_config_item` WHERE `status` LIKE 'Queued' AND `kiosk_id` = 4 ORDER BY `status` DESC

 

 public function getValueOptions($config_name)
 {
      $this->db->select('name,value_options');
      $this->db->from(self::TABLE);
      $this->db->where(array('id'=> $config_name));
      $query = $this->db->get();

    //echo 111111;
    //  print_r($this->db->last_query());

      if($query->num_rows() > 0){
        $data = $query->result();
        return $data[0];
      } else{
        return false;
      }
 }

}
