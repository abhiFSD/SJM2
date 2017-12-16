<?php
 class Kioskmodel_model extends CI_Model
 {
   const TABLE = 'kiosk_model';
   const KIOSK_CONFIG_TABLE = 'kiosk_model_config_item';


   /**
    * Get all the details
    *
    * @return array
    */
    public function getAll()
    {
      $query = $this->db->get(self::TABLE);
      $results = $query->result();

  		return $results;
    }


    /**
     *
     * Get all the values based on the ID
     *
     * @param integer $id
     * @return string
     */
    public function getOneById($id)
    {
      $query = $this->db->get_where(self::TABLE,array('id' => $id));
      if ($query->num_rows() > 0) {
        $data = $query->result();

        return $data[0];
      } else {

        return false;
      }
    }

 /**
 *
 * Get all the configurations related to a kiosk model
 * @param integer $id
 * @return string
 */
    public function getKioskConfig($id)
    {
      $this->db->select('k.id,k.kiosk_model_id,k.config_item_id,k.value,ci.name as configuration_name');
      $this->db->from(self::KIOSK_CONFIG_TABLE.' as k');
      $this->db->join('config_item as ci','ci.id = k.config_item_id');
      $this->db->where('k.kiosk_model_id',$id);

      $query = $this->db->get();

      if ($query->num_rows() > 0) {
        $data = $query->result();

        return $data;
      } else {
        return false;
      }
    }

    /**
    *
    *function to add or edit the kioskmodel details
    * @param integer $id
    * @return
    */

    public function editModel($id)
    {
      $data = array(
        'name'        => $this->input->post('model_name'),
        'make'        => $this->input->post('model_make'),
        'status'      => $this->input->post('status')

      );
      $this->db->update(self::TABLE,$data,array('id'=>$id)); //Updated kiosk_model table with values from form field

      if($this->input->post('max_shelves')){
         $value = $this->input->post('max_shelves');

         $conf_name = $this->modelConfigurations('Maximum shelves');
         $confId = $conf_name->id;
         $edited_val = $this->updateModelConfiguration($id,$confId,$value);
         if($edited_val > 0){
           $msg = "Kiosk model configurations updated successfully";
         } else {
           $msg = "Kiosk model configurations added successfully";
         }
      } else {
        $conf_name = $this->modelConfigurations('Maximum shelves');
        $confId = $conf_name->id;
        $this->deleteModelConfiguration($id,$confId);
      }

      if($this->input->post('model_height')){
         $value = $this->input->post('model_height');

         $conf_name = $this->modelConfigurations('Height');
         $confId = $conf_name->id;
         $edited_val = $this->updateModelConfiguration($id,$confId,$value);
         if($edited_val > 0){
           $msg = "Kiosk model configurations updated successfully";
         } else {
           $msg = "Kiosk model configurations added successfully";
         }

      }else {
        $conf_name = $this->modelConfigurations('Height');
        $confId = $conf_name->id;
        $this->deleteModelConfiguration($id,$confId);
      }

      if($this->input->post('model_width')){
         $value = $this->input->post('model_width');

         $conf_name = $this->modelConfigurations('Width');
         $confId = $conf_name->id;
         $edited_val = $this->updateModelConfiguration($id,$confId,$value);
         if($edited_val > 0){
           $msg = "Kiosk model configurations updated successfully";
         } else {
           $msg = "Kiosk model configurations added successfully";
         }

      }else {
        $conf_name = $this->modelConfigurations('Width');
        $confId = $conf_name->id;
        $this->deleteModelConfiguration($id,$confId);
      }

      if($this->input->post('model_depth')){
         $value = $this->input->post('model_depth');

         $conf_name = $this->modelConfigurations('Depth');
         $confId = $conf_name->id;
         $edited_val = $this->updateModelConfiguration($id,$confId,$value);
         if($edited_val > 0){
           $msg = "Kiosk model configurations updated successfully";
         } else {
           $msg = "Kiosk model configurations added successfully";
         }

      }else {
        $conf_name = $this->modelConfigurations('Depth');
        $confId = $conf_name->id;
        $this->deleteModelConfiguration($id,$confId);
      }

      if($this->input->post('model_weight')){
         $value = $this->input->post('model_weight');

         $conf_name = $this->modelConfigurations('Weight');
         $confId = $conf_name->id;
         $edited_val = $this->updateModelConfiguration($id,$confId,$value);
         if($edited_val > 0){
           $msg = "Kiosk model configurations updated successfully";
         } else {
           $msg = "Kiosk model configurations added successfully";
         }

      } else {
        $conf_name = $this->modelConfigurations('Weight');
        $confId = $conf_name->id;
        $this->deleteModelConfiguration($id,$confId);
      }

    }
    /**
    * Function to add a new Kiosk model
    * @return
    *
    */
    public function addModel()
    {
      $data = array(
        'name'        => $this->input->post('model_name'),
        'make'        => $this->input->post('model_make'),
        'status'      => $this->input->post('status')

      );
      $this->db->insert(self::TABLE,$data);
      $id = $this->db->insert_id();
      if($this->input->post('max_shelves')){
         $value = $this->input->post('max_shelves');

         $conf_name = $this->modelConfigurations('Maximum shelves');
         $confId = $conf_name->id;
         $this->updateModelConfiguration($id,$confId,$value);
         $msg = "Kiosk model configurations added successfully";
         }

      if($this->input->post('model_height')){
         $value = $this->input->post('model_height');

         $conf_name = $this->modelConfigurations('Height');
         $confId = $conf_name->id;
         $this->updateModelConfiguration($id,$confId,$value);
           $msg = "Kiosk model configurations added successfully";
        }
      if($this->input->post('model_width')){
         $value = $this->input->post('model_width');

         $conf_name = $this->modelConfigurations('Width');
         $confId = $conf_name->id;
         $this->updateModelConfiguration($id,$confId,$value);
           $msg = "Kiosk model configurations added successfully";
        }
      if($this->input->post('model_depth')){
         $value = $this->input->post('model_depth');

         $conf_name = $this->modelConfigurations('Depth');
         $confId = $conf_name->id;
         $this->updateModelConfiguration($id,$confId,$value);
         $msg = "Kiosk model configurations added successfully";
      }
      if($this->input->post('model_weight')){
         $value = $this->input->post('model_weight');

         $conf_name = $this->modelConfigurations('Weight');
         $confId = $conf_name->id;
         $this->updateModelConfiguration($id,$confId,$value);
         $msg = "Kiosk model configurations added successfully";
       }

    }
    /**
    * function to get the id of a Configuration name from the config item table
    * @param string $name
    * @return array
    */

    private function modelConfigurations($name)
    {

      $this->db->like('name',$name);
      $query = $this->db->get('config_item');

      if($query->num_rows() > 0){
        $data = $query->result();

        return $data[0];
      } else{
        return false;
      }
    }



    /**
    * function to get the add/edit the Kiosk Model Configurations in the kiosk_model_config_item TABLE
    * @params integer $kiosk_model_id ,integer $config_item_id ,integer $value
    * @return integer
    */
    private function updateModelConfiguration($kioskId,$configItemId,$value)
    {
      $query = $this->db->get_where(self::KIOSK_CONFIG_TABLE,array('kiosk_model_id'=>$kioskId,'config_item_id'=>$configItemId));
      if($query->num_rows() > 0){
        $this->db->set('value',$value);
        $this->db->where(array('kiosk_model_id'=>$kioskId,'config_item_id'=>$configItemId));
        $this->db->update(self::KIOSK_CONFIG_TABLE);
        return 0;

      } else{

        $this->db->insert(self::KIOSK_CONFIG_TABLE,array('kiosk_model_id'=>$kioskId,'config_item_id'=>$configItemId,'value'=>$value));
        return $this->db->insert_id();
      }

    }

    /**
    * Function to delete a particular Model Configuration if no value exists
    * @param integer $kiosk_model_id ,integer $config_item_id
    *
    */

    private function deleteModelConfiguration($kioskId,$configItemId)
    {
      $query = $this->db->get_where(self::KIOSK_CONFIG_TABLE,array('kiosk_model_id'=>$kioskId,'config_item_id'=>$configItemId));
      if($query->num_rows() > 0){
        $this->db->where(array('kiosk_model_id'=>$kioskId,'config_item_id'=>$configItemId));
        $this->db->delete(self::KIOSK_CONFIG_TABLE);
      }
    }


}
