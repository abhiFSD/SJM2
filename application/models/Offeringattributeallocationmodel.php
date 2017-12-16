<?php
class Offeringattributeallocationmodel extends CI_Model
{
    const TABLE = 'offering_attribute_allocation'; // assigning the database to a constant

    /**
     * Function to get all the offering attributes and values for a corresponding kiosk
     * @param integer $id
     * @return array
     *
     */
    public function getById($id)
    {
        $query = $this->db->get_where(self::TABLE, array('id' => $id));

        if ($query->num_rows() > 0) {
            $data = $query->result();
            //print_R($data)
            return $data[0];
        } else {

            return false;
        }
    }

    public function getKioskByPosition($position)
    {
        $query =   $this->db->query("SELECT distinct kiosk_id from `offering_attribute_allocation`  WHERE  position='$position'");

        if($query->num_rows() > 0){
           return $query->result();
        }
        else{
          return false;
        }    
    }

    public function getByKioskId($id)
    {
        $this->db->distinct();
        $this->db->select('position');
        $this->db->from(self::TABLE);
        $this->db->where('kiosk_id',$id);
        $results = $this->db->get()->result_array();

        $result = array();
        foreach ($results as $key => $value) {
            $result[] = $value['position'];
        }
        return $result;
    }

    /*
     * Function: get_all_positions()
     * Description: This will get all the positions from the db
     *
     *
     * @author: Avtar Gaur (developer.avtargaur@gmail.com)
     * Date: Jan 22, 2017
     */
    public function get_all_positions()
    {

        $this->db->distinct();
        $this->db->select('position');
        $results = $this->db->get(self::TABLE)->result_array();

        $result = array();
        foreach ($results as $key => $value) {
            $result[] = $value['position'];
        }
        return $result;
    }

    /*
     * Function: get_values_for_variable()
     * Description: This will get all the values from the db for a variable $variable
     *
     * @param: $variable
     * @author: Avtar Gaur (developer.avtargaur@gmail.com)
     * Date: Jan 22, 2017
     */
    public function get_values_for_variable($variable = '')
    {
        $this->db->distinct();
        $this->db->select('value');
        $this->db->from(self::TABLE . ' as oaa');
        $this->db->join('offering_attribute as oa', 'oa.id = oaa.offering_attribute_id');
        $this->db->where('oa.name', $variable);
        $this->db->order_by('oaa.value', 'DESC');

        $query = $this->db->get();
        $results = $query->result_array();
        $result = array();
        foreach ($results as $key => $value) {
            $result[] = $value['value'];
        }

        return $result;
    }

    /*
     * Function: get_products_category()
     * Description: This will get all the values from the db for a variable $variable
     *
     * @param: $variable
     * @author: Shamim Ahmed
     * Date: Jun 16, 2017
     */
    public function get_products_category($variable = '')
    {
        $this->db->distinct();
        $this->db->select('sku.name, sku.id, sku.sku_value as sku_name');
        $this->db->from('item');
        $this->db->join('sku', 'sku.product_id = item.id');
        $this->db->where('product_category_id', $variable);

        $query = $this->db->get();
        $results = $query->result_array();

        return $results;
    }

    /**
     * Function to get all the attributes of a KIOSK at a specified position
     * @param integer $position ,integer $kioskID
     * @return array
     *
     */
    public function getAllByPosition($position, $kioskID)
    {
        $this->db->select('oaa.value,oa.name');
        $this->db->from(self::TABLE . ' as oaa');
        $this->db->join('offering_attribute as oa', 'oa.id = oaa.offering_attribute_id');
        $conds = array('oaa.kiosk_id' => $kioskID, 'oaa.position' => $position);
        $this->db->where($conds);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return ($query->result());
        } else {
            return false;
        }
    }

    /**
     *
     * @param $kioskId
     * @param $offeringAttributeId
     * @param $position
     * @return bool
     */
    public function getAllocation ($kioskId, $position, $offeringAttributeId = "", $status = "")
    {
        $this->db->where( array('kiosk_id' => $kioskId,
                                                            'position' => $position));

        if($offeringAttributeId) {
            $this->db->where('offering_attribute_id', $offeringAttributeId);
        }
        if ($status) {
            $this->db->where('status', $status);
        }
        $query = $this->db->get(self::TABLE);

        if ($query->num_rows() > 0) {
            $data = $query->result();
            return $data;
        } else {
            return false;
        }

        return false;
    }

    /**
     *
     * @param $kioskId
     * @param $offeringAttributeId
     * @param $position
     */
    public function checkStatus($kioskId, $offeringAttributeId, $position)
    {

    }

    public function queue ($data = array())
    {
       $this->load->model('Productmodel');
        foreach ($data['allocation'] as $allocationId)
        {
            $allocation = $this->getById($allocationId);

            for ($i=0; $i < count($data['attributes']); $i++) {

                $queuedAllocation = $this->getAllocation($allocation->kiosk_id, $allocation->position,
                                                                $data['attributes'][$i], "Queued");
                // if already queued attribute allocation, delete it
                if ($queuedAllocation) {
                    $this->db->delete(self::TABLE, array('id' => $queuedAllocation[0]->id));
                }
                if ($data['attributes'][$i] == 1) {
                    // need to get the SKU ID from the product id
                    $productData = $this->Productmodel->getSkuIdfromProductId($data['values'][$i]);

                    $value = $productData[0]['squid'];
                } else {
                    $value = $data['values'][$i];
                }

                if ($data['status'][$i] == 1) {
                    $status = "Permanent";
                } else {
                    $status = "Temporary";
                }

                $insertData = array (
                    'position' => $allocation->position,
                    'kiosk_id' => $allocation->kiosk_id,
                    'offering_attribute_id' => $data['attributes'][$i],
                    'value'  => $value,
                    'status' => 'Queued',
                    'date_queued' => date('Y-m-d'),
                    'user_queued' => $this->session->userdata('user_id'), //@todo add the userid
                    'commit_type' => $status,
                );

                $this->db->insert(self::TABLE, $insertData);
            }
        }
    }

    public function commit($data = array())
    {
        foreach ($data['allocation'] as $allocationId) {
            $allocation = $this->getById($allocationId);
            if ($allocation) {

                $attributeStatus = array();
                // get the queued one to be active
                $queuedAllocations = $this->getAllocation($allocation->kiosk_id,
                    $allocation->position, '', 'Queued');

                // get the active ones to make it inactive
                $activeAllocations = $this->getAllocation($allocation->kiosk_id,
                    $allocation->position, '', 'Active');

                $date = new DateTime($data['date']);

                if ($queuedAllocations) {
                    foreach ($queuedAllocations as $queuedAllocation) {
                        $updateData = array('status' => 'Active',
                            'date_applied' => $date->format('Y-m-d H:i:s'),
                            'user_applied' => $this->session->userdata('user_id')
                        );

                        $this->db->update(self::TABLE, $updateData, array('id' => $queuedAllocation->id));

                        if ($queuedAllocation->commit_type == "Permanent") {
                            $attributeStatus[$queuedAllocation->offering_attribute_id] = "Permanent";
                        } else {
                            $attributeStatus[$queuedAllocation->offering_attribute_id] = "Temporary";
                        }
                    }
                }

                if ($activeAllocations) {
                    foreach ($activeAllocations as $activeAllocation) {

                        if (isset($attributeStatus[$activeAllocation->offering_attribute_id])) {
                            if ($attributeStatus[$activeAllocation->offering_attribute_id] == "Permanent") {
                                $status = "Inactive";
                            } else {
                                $status = "Temp Inactive";
                            }
                            $updateData = array(
                                'status' => $status,
                                'date_unapplied' => $date->format('Y-m-d H:i:s'),
                                'user_unapplied' => $this->session->userdata('user_id')
                            );

                            $this->db->update(self::TABLE, $updateData, array('id' => $activeAllocation->id));
                        }
                    }
                }
            }
        }
    }

    /**
     *
     * Delete queued items for the selected allocations
     * @param array $data
     */
    public function unqueue ($data = array())
    {

        foreach ($data['allocation'] as $allocationId)
        {
            $allocation = $this->getById($allocationId);

            if ($allocation) {

                $queuedAllocations = $this->getAllocation($allocation->kiosk_id,
                                                        $allocation->position, '','Queued');

                if ($queuedAllocations) {
                    foreach ($queuedAllocations as $queuedAllocation) {
                        $this->db->delete(self::TABLE, array('id' => $queuedAllocation->id));
                    }
                }
            }
        }
    }

    public function saveNewPositionAndData($data)
    {
        $this->load->model('Productmodel');
        $attributeCount = count($data['attributes']);

        for ($i=0; $i < $attributeCount; $i++) 
        {
            if ($data['attributes'][$i] == 1) 
            {
                // need to get the SKU ID from the product id
                $productData = $this->Productmodel->getSkuIdfromProductId($data['attributes'][$i]);
                $value = $productData[0]['squid'];
            } 
            else 
            {
                $value = $data['values'][$i];
            }

            if ($data['status'][$i] == 1) 
            {
                $status = "Permanent";
            } 
            else 
            {
                $status = "Temporary";
            }

            $insertData = array(
                'position' => $data['position'],
                'kiosk_id' => $data['kioskId'],
                'offering_attribute_id' => $data['attributes'][$i],
                'value' => $value,
                'status' => 'Queued',
                'date_queued' => date('Y-m-d'),
                'user_queued' => '', //@todo add the userid
                'commit_type' => $status,
            );
            $this->db->insert(self::TABLE, $insertData);
        }
    }

    public function doDeleteOfferingQueue($item)
    {
        $updateData = array(
           'queue_type' => 'delete',
           'date_queued' => date('Y-m-d H:i:s'),
           'user_applied' => $this->session->userdata('user_id')
        );

        return $this->db->update(self::TABLE, $updateData, array('kiosk_id' => $item[0],'position' => $item[1] ));
    }

    public function batchmodifyqueue($data)
    {
        $selected_ids = json_decode($data['json']);
        $isOk = true;

        foreach ($selected_ids as $kiosk)
        {
            $kiosk_id = $kiosk[0];
            $position = $kiosk[1];

            foreach ($data['attribute'] as $attribute_id => $value)
            {
                if (empty($value) && $value !=='0') continue;
                if (empty($attribute_id)) continue;

                $datetime = date('Y-m-d H:i:s');

                // remove current queued attribute
                $this->db
                    ->where('kiosk_id', $kiosk_id)
                    ->where('position', $position)
                    ->where('offering_attribute_id', $attribute_id)
                    ->where('status', 'Queued')
                    ->delete(self::TABLE);

                // new attribute will be queued
                $outcome = $this->db
                    ->set('status', 'Queued')
                    ->set('date_queued', $datetime)
                    ->set('queue_type', 'update')
                    ->set('user_queued', $this->session->userdata('user_id'))
                    ->set('kiosk_id', $kiosk_id)
                    ->set('position', $position)
                    ->set('offering_attribute_id', $attribute_id)
                    ->set('value', $value)
                    ->set('isOptional', (!empty($data['optional'][$attribute_id]) && $data['optional'][$attribute_id]=="on") ? 1 : 0)
                    ->set('commit_type', $data['attributestatus'][$attribute_id])
                    ->insert('offering_attribute_allocation');

                if (!$outcome)
                {
                    $isOk = false;
                }
            }
        }  

        return $isOk;
    }


    public function addpositionqueue($data)
    {
        $isOk = true;
        $this->load->model('Productmodel');     
        foreach ($data['kiosk-numbers'] as $key => $kiosk)
        {
            $insertData = array(
              'position' => $data['position-number'],
              'kiosk_id' => $kiosk,
              'offering_attribute_id' => 4,
              'value' => 0,
              'queue_type' => 'add',
              'status' => 'Active',
              'date_queued' => date('Y-m-d H:i:s'),
              'date_applied' => date('Y-m-d H:i:s'),
              'user_applied' => $this->session->userdata('user_id'),
              'user_queued' =>  $this->session->userdata('user_id')
            );

            $outcome  = $this->db->insert(self::TABLE, $insertData);
            if($outcome ==  false)
            {
                $isOk = false;
            }
        }  
 
        return $isOk;
    }
 
    public function doCommitFromAtKiosk($kiosk_id, $position, $datetime)
    {
        $updateData = array(
            'queue_type' => 'none',
            'date_queued' => $datetime,
            'date_applied' => date('Y-m-d H:i:s'),
            'status' => 'Active',
            'user_applied' => $this->session->userdata('user_id')
        );

        $DeleteData = $this->db->delete(self::TABLE, array('kiosk_id' => $kiosk_id, 'position'=>$position, 'status' => 'Queued'));    

        $UpdateData = $this->db->update(self::TABLE, $updateData, array('kiosk_id' => $kiosk_id, 'position'=>$position, 'status' => 'Queued')); 

        return $UpdateData;
    }

    public function doCommitFromQueue($kiosk_id, $position)
    {
        $DeleteData = $this->db->delete(self::TABLE, array('kiosk_id' => $kiosk_id, 'position'=>$position, 'queue_type' => 'delete'));     

        return $DeleteData;
    }

    public function doBatchUnQueue($item)
    {
        $DeleteData = $this->db->delete(self::TABLE, array('kiosk_id' => $item[0], 'position'=>$item[1], 'status' => 'Queued'));

        POW\OfferingAttributeAllocation::set_none($item[0], $item[1]);

        return $DeleteData;
    }

}
