<?php

class ProductattributeModel extends CI_Model
{

	const TABLE = 'item_attribute';
    const ATTRIBUTE_APPLICATION_TABLE = 'item_attribute_application';
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

		if ($query->num_rows() > 0) {
			$data = $query->result();

			return $data[0];
		} else {

			return false;
		}
	}



    public function getOptionById($id)
    {
        $query = $this->db->get_where('item_attribute_option', array('id' => $id));

        if ($query->num_rows() > 0) {
            $data = $query->result();

            return $data[0];
        } else {

            return false;
        }
    }



    /**
     * Get all the details
     *
     * @return array
     */
    public function getAllWithOptions()
    {
       
        $sql  = "SELECT item_attribute.*  ,GROUP_CONCAT(item_attribute_option.name SEPARATOR ', ' ) AS options  FROM item_attribute left join item_attribute_option on item_attribute.id = item_attribute_option.product_attribute_id GROUP by item_attribute.id";



//SELECT *  ,GROUP_CONCAT(item_attribute_option.name) AS options  FROM product_attribute left join product_attribute_option on product_attribute.id = item_attribute_option.product_attribute_id GROUP by item_attribute_option.product_attribute_id
       $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }
	/**
	 * Get all the details
	 *
	 * @return array
	 */
	public function getAll()
	{
		$this->db->select();
		$this->db->from(self::TABLE);
		$query = $this->db->get();


//SELECT *  ,GROUP_CONCAT(item_attribute_option.name) AS options  FROM product_attribute left join product_attribute_option on product_attribute.id = item_attribute_option.product_attribute_id GROUP by item_attribute_option.product_attribute_id
		$results = $query->result_array();

		return $results;
	}



    /**
     * Get attributes based on the ID
     * @param $attributeId
     * @return mixed
     */
    public function getAttributeOptionValues($attributeId)
    {
        $this->db->select('p.*');
        $this->db->from('item_attribute_option as p');
        $this->db->where("p.product_attribute_id = '".$attributeId."'");
        $query = $this->db->get();

        $results = $query->result_array();

        return $results;

    }



    /**
     * Save Attribute along with the unit of measure
     * @param $params
     */
    public function saveAttribute($params)
    {

        $data = array ('name' => $params['attribute_name'], 'unit_of_measure_id' => $params['unit_of_measure_id']);

        $this->db->insert('item_attribute', $data);
        return $this->db->insert_id();
    }


    /**
     * Update Attribute along with the unit of measure
     * @param $params
     */
    public function updateAttribute($params)
    {

        $data = array ('name' => $params['attribute_name'], 'unit_of_measure_id' => $params['unit_of_measure_id']);

        $out =   $this->db->update('item_attribute', $data,array('id' =>$params['attribute_id']));

       // print_r($out);

        return $out;
    }


    /**
     * Save attribute option values
     *
     * @param $params
     */
    public function saveAttributeOption($params)
    {

        $data = array(
            'name' => $params['name'],
            'sku_suffix' => $params['sku_suffix'],
            'product_attribute_id' => $params['attribute_id']
        );

        $this->db->insert('item_attribute_option', $data);

        return $this->db->insert_id();
    }


    /**
     * Update attribute option values
     *
     * @param $params
     */
    public function updateAttributeOption($params)
    {

        $data = array(
            'name' => $params['name'],
            'sku_suffix' => $params['sku_suffix'] 
            
        );

     // $out =   $this->db->update('item_attribute_option', $data, array('id',$params['attribute_id']));

        return  $out;
    }

    /*
     * Function: save_multiple_product_attribute_options()
     * Description: This will save the batch of product attribute options in the database
     *
     * @param: $product_attribute_id
     * Return Value: array
     * @author: Avtar Gaur(developer.avtargaur@gmail.com)
     * Date: Dec 22, 2016
     */
    public function save_multiple_product_attribute_options($product_attribute_id = 0){
        // no of attribute options
        $count = count($this->input->post('attribute_option_name'));

        // should work only when there are attribute options sent from the form
        if($count){
            $attribute_option_names = $this->input->post('attribute_option_name');



            $attribute_option_suffixes = $this->input->post('attribute_option_suffix');

            $array_for_batch = array();


            // creating the array to be inserted in the batch
            for ($i=0; $i < $count; $i++) {
                if(!isset($attribute_option_suffixes[$i]))
                {
                    $attribute_option_suffixes[$i] = '';
                }
                $array_for_batch[] = array('name' => $attribute_option_names[$i], 'sku_suffix' => $attribute_option_suffixes[$i], 'product_attribute_id' => $product_attribute_id);
            }
            

         //   print_r($array_for_batch);

            // inserting batch
            $this->db->insert_batch('item_attribute_option', $array_for_batch);
        }
    }



    /*
     * Function: save_multiple_product_attribute_options()
     * Description: This will save the batch of product attribute options in the database
     *
     * @param: $product_attribute_id
     * Return Value: array
     * @author: Avtar Gaur(developer.avtargaur@gmail.com)
     * Date: Dec 22, 2016
     */
    public function remove_multiple_product_attribute_options($ids){
    
        if (count($ids) > 0) {

        $this->db->where_in('id', $ids);
        $out =   $this->db->delete('item_attribute_option');

      return $out;
        }else
        {
            return 0;
        }

    }



    /*
     * Function: get_all_units()
     * Description: This will get all the units from the product_attribute table
     *
     * @param: NULL
     * Return Value: array
     * @author: Avtar Gaur(developer.avtargaur@gmail.com)
     * Date: Dec 21, 2016
     */
    public function get_all_units(){
        $this->db->select('Distinct(unit_of_measure_id)');
        $this->db->from(self::TABLE. ' as p');    
        $this->db->where("p.unit_of_measure_id != '0'");
        $query = $this->db->get();

        return $query->result_array();
    }

    /*
     * Function: save_product_attribute_application()
     * Description: This will insert product related product attribute option data in the 
     * item_attribute_application table
     *
     * @param: NULL
     * Return Value: array
     * @author: Avtar Gaur(developer.avtargaur@gmail.com)
     * Date: Dec 23, 2016
     */

    public function save_product_attribute_application($product_id = 0, $attribute_options_data = array()){
        $batch_data = array();

        // preparing the data to be inserted
        foreach ($attribute_options_data as $key => $values) {
            // if multiple option type of product attribute then get all the selected options
            if(is_array($values)){
                foreach ($values as $value) {
                    $batch_data[] = array(
                                        'product_attribute_id' => $key,
                                        'product_attribute_option_id' => $value,
                                        'value' => NULL,
                                        'product_id' => $product_id,
                                    );
                }
            }else{
                // else save it as the text type of attribute
                $batch_data[] = array(
                                        'product_attribute_id' => $key,
                                        'product_attribute_option_id' => NULL,
                                        'value' => $values,
                                        'product_id' => $product_id
                                    );
            }
        }
        // now insert into the database
        $this->db->insert_batch(self::ATTRIBUTE_APPLICATION_TABLE, $batch_data);
    }


    public function remove_previous_product_attribute_application($product_id = 0, $attribute_options_data = array()){
        $batch_data = array();

        $this->db->where(array('product_id'=>$product_id));
        $this->db->delete(self::ATTRIBUTE_APPLICATION_TABLE);
    }

 
    /**
     * Get all the details
     *
     * @return array
     */
    public function getAllAtributesOptions($id)
    {

         $query = $this->db->get_where('item_attribute_option', array('product_attribute_id' => $id));
         $results = $query->result_array(); 
 
        return (object) $results;
    }


    /**
     * Get all the details
     *
     * @return array
     */
    public function getAllAtributesOptionsSelected($id)
    {
        $query = $this->db->get_where('item_attribute_application', array('product_id' => $id));
        $results = $query->result_array(); 

        $out = array();

        foreach ($results as $key => $result) 
        {
            if($result['product_attribute_option_id']!="")
                $out[$result['product_attribute_id']][$result['product_attribute_option_id']]= $result;
            else
                $out[$result['product_attribute_id']]= $result;
        }
       
        return  $out;
    }


}
