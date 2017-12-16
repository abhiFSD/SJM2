<?php
class KioskModel extends CI_Model
{
    const TABLE = 'kiosk';
    const MODELTABLE ='kiosk_model';
    const CONFIGTABLE = 'kiosk_config_item';
    const KIOSKLOCATIONMODEL = 'kiosk_location';

    public function addKioskLocation($data)
    {
        $this->db->insert('kiosk_location', $data);

        return $this->db->insert_id();
    }

    public function getKioskLocationInfo($id)
    {
        $query = $this->db
            ->select('k.id,  k.name,s.state,s.name as sitename')
            ->from('kiosk_location as k')
            ->join('site as s','k.site_id = s.id')
            ->where('k.status', 'Active')
            ->where('k.id', $id)
            ->get();

        return $query && $query->num_rows() ? $query->row() : false;
    }

    /**
     *
     * Function to retrive all the kiosk and the name of the corresponding model from the kiosk
     * and kiosk_model table. This is used in the Batch offering change.
     * @return array
     *
     */
    public function getKiosks_names()
    {
        $this->db->select('k.id,k.number,k.kiosk_model_id,k.status,km.id as model_id,km.name as model_name');
        $this->db->from(self::TABLE.' as k');
        $this->db->join(self::MODELTABLE.' as km' , 'km.id = k.kiosk_model_id');
        $query = $this->db->get();
        $results = $query->result();

        return $results;
    }

    /**
     * Function to get all the kiosk Models
     *
     * @return array
     *
     */
    public function getkioskModels()
    {
        $this->db->select('name,make,id');
        $this->db->from(self::MODELTABLE);
        $this->db->where('status','Active');
        $query =$this->db->get();
        $results = $query->result();

        return $results;
    }

    /**
     * Function to find all the party names associated with a party_type_allocation of Kiosk Owner
     * @param int party_type_id
     * @return array
     *
     */
    public function findPartyName($party_type_id)
    {
        $this->db->select('pa.party_type_id,pa.party_id,p.display_name as alias_name');
        $this->db->from('party_type_allocation as pa');
        $this->db->join('party as p','p.id = pa.party_id','left');
        $this->db->where(array('pa.party_type_id'=>$party_type_id));

        $query = $this->db->get();
        $results = $query->result();

        return $results;
    }

    /**
     * function to get all kiosk information for a corresponding kiosk id
     * @param integer kiosk id
     * @return array
     *
     */
    public function getKioskById($id)
    {
        $this->db->select('k.id,k.number,k.kiosk_model_id,k.status,k.party_type_allocation_id,k.warranty_parts,k.warranty_labour,k.date_purchased');
        $this->db->from(self::TABLE.' as k');
        $this->db->where(array('k.id'=> $id));

        $query = $this->db->get();

        if($query->num_rows() > 0)
        {
            $data = $query->result();

            return $data[0];
        }

        return false;
    }

    public function getModelConfigs($kiosk_model_id)
    {
        $this->db->select('kc.value, c.name as configuration_name,c.unit_of_measure');
        $this->db->from('kiosk_model_config_item as kc');
        $this->db->join('config_item as c','c.id = kc.config_item_id');
        $this->db->where('kc.kiosk_model_id',$kiosk_model_id);

        $query = $this->db->get();

        $results = $query->result();

        return $results;
    }

    /**
     * Function to get all configurations related to a particular kiosk  from the config_item table based on the kiosk id
     * @param integer $kiosk_id
     * @return array
     *
     */
    public function getKioskConfigs($kiosk_id)
    {
        $this->db->select('kc.config_item_id ,kc.value,c.name,c.value_options');
        $this->db->from(self::CONFIGTABLE . ' as kc');
        $this->db->join('config_item as c','c.id = kc.config_item_id');
        $this->db->where('kc.kiosk_id',$kiosk_id);
        $this->db->where('kc.status','Active');

        $query = $this->db->get();

        return ($query->result());
    }

    public function getKioskConfigsQueued($kiosk_id)
    {
        $this->db->select('kc.config_item_id ,kc.value,c.name,c.value_options');
        $this->db->from(self::CONFIGTABLE . ' as kc');
        $this->db->join('config_item as c','c.id = kc.config_item_id');
        $this->db->where('kc.kiosk_id',$kiosk_id);
        $this->db->where('kc.status','Queued');

        $query = $this->db->get();

        return ($query->result());
    }

     /**
     * Function to get values of multiple drop down select
     * @param integer $kioskId
     * return array
     *
     */
     public function getKioskMultipleConfigs($kiosk_id){
       $this->db->select('kc.config_item_id ,kc.value,c.name');
       $this->db->from(self::CONFIGTABLE . ' as kc');
       $this->db->join('config_item as c','c.id = kc.config_item_id');
       $this->db->where('c.field_type = "Dropdown – Multi Select"');
       $this->db->where('kc.kiosk_id',$kiosk_id);
       $query = $this->db->get();

       return ($query->result());
     }

    /**
     * Function to get all the option values of each configuration
     * @return array
     *
     */
    public function getAllKioskConfigurationOptions()
    {
        $this->db->select('name,field_type,value_options,id');
        $this->db->from('config_item');
        $this->db->where('apply_to','kiosk');
        $query = $this->db->get();

        return ($query->result());
    }

    public function checkOfferingAttributeAllocation($id)
    {
        $this->db->select('oa.id as offering_attribute_allocation_id, k.id');
        $this->db->from('offering_attribute_allocation as oa');
        $this->db->join(self::TABLE . ' as k', 'k.id = oa.kiosk_id');
        $this->db->where('k.id', $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    public function updateKioskConfiguration($kiosk_id, $status, $config_item_id, $value)
    {
        $query = $this->db->select('id')->from(self::CONFIGTABLE)->where('config_item_id', $config_item_id)->where('kiosk_id', $kiosk_id)->where('status', $status)->get();
        $num_rows = $query->num_rows();
        $now = new DateTime();
        $now = $now->format('Y-m-d H:m:s');
        if ($status == 'Active')
        {
            $this->db->set('date_applied', $now)->set('date_last_updated', $now)->set('user_applied', $this->session->userdata('name'));
        }

        if ($status == 'Queued')
        {
            $this->db->set('date_queued', $now)->set('user_queued', $this->session->userdata('name'));
        }

        if ($num_rows)
        {
            $this->db->set('value', $value)->where('id', $query->row()->id)->update(self::CONFIGTABLE);
            return 0;
        }
        else
        {
            $this->db->set('value', $value)->set('config_item_id', $config_item_id)->set('status', $status)->set('kiosk_id', $kiosk_id)->insert(self::CONFIGTABLE);
            return $this->db->insert_id();
        }
    }

    /**
     * This function is to delete a kiosk configuration from the kiosk_config_item table if it exists
     * @param integer kiosk_id , integer config_item_id
     *
     *
     */
    private function deleteKioskConfiguration($kioskId, $configItemId)
    {
        $query = $this->db->get_where(self::CONFIGTABLE, array(
            'kiosk_id' => $kioskId,
            'config_item_id' => $configItemId
        ));

        if ($query->num_rows() > 0)
        {
            $this->db->where(array(
                'kiosk_id' => $kioskId,
                'config_item_id' => $configItemId
            ));
            $this->db->delete(self::CONFIGTABLE);
        }
    }

    public function deleteKioskConfig($kioskId, $configItemId)
    {
        $query = $this->db->query("SELECT * FROM `kiosk_config_item` WHERE `config_item_id` = $configItemId AND `kiosk_id` = $kioskId order by id desc limit 1");
        if ($query->num_rows() > 0)
        {
            $data = $query->result();
            $data = $data[0];

            $this->db->where(array(
                'id' => $data->id
            ));
            $this->db->delete(self::CONFIGTABLE);
        }
    }

    /**
     * function to update multiple kiosk configurations
     * @param integer $kioskId, integer $config_itm_id , string $value
     *
     */
    private function updatemultipleKioskConfiguration($kiosk_id, $config_itm_id, $value)
    {
        $now = new DateTime();
        $now = $now->format('Y-m-d H:m:s');
        $this->db->insert(self::CONFIGTABLE, array(
            'value' => $value,
            'config_item_id' => $config_itm_id,
            'status' => 'Active',
            'kiosk_id' => $kiosk_id,
            'date_applied' => $now
        ));
        return $this->db->insert_id();
    }

    /**
     * function to insert offering attributes for a particular kiosk
     * @param integer $position, integer $offering_attribute_id ,$value , $kiosk_id
     *
     *
     */
    private function insertOfferingAttributes($position, $offeringAttributeId, $value, $kiosk_id)
    {
        $now = new DateTime();
        $now = $now->format('Y-m-d H:m:s');
        $data = array(
            'position' => $position,
            'offering_attribute_id' => $offeringAttributeId,
            'value' => $value,
            'kiosk_id' => $kiosk_id,
            'status' => 'Active',
            'date_applied' => $now
        );
        $this->db->select();
        $query = $this->db->get_where('offering_attribute_allocation', array(
            'position' => $position,
            'offering_attribute_id' => $offeringAttributeId,
            'value' => $value,
            'kiosk_id' => $kiosk_id
        ));

        if ($query->num_rows() > 0)
        {
            return 0;
        }
        else
        {
            $this->db->insert('offering_attribute_allocation', $data);
            return $this->db->insert_id();
        }
    }

    /**
     * Function to find the offering_attribute id given the name
     * @param string $name
     * @return object
     *
     */
    private function findOfferingAttributeId($name)
    {
        $this->db->select('id');

        $query = $this->db->get_where('offering_attribute', array(
            'name' => $name
        ));

        if ($query->num_rows() > 0)
        {
            $data = $query->result();
            return $data[0];
        }

        return false;
    }

    /**
     * function to find the sku ID for given sku_value
     * @param string $sku_value
     * @return object
     */
    private function findSKUID($sku_value)
    {
        $this->db->select('id');
        $query = $this->db->get_where('sku', array(
            'sku_value' => $sku_value
        ));
        if ($query->num_rows() > 0)
        {
            $data = $query->result();
            return $data[0];
        }

        return false;
    }

    public function getKioskByConditionPick($kiosk_id)
    {
        $query1 = $this->db
            ->select('kci.value, kci.id, kci.config_item_id, config_item.name')
            ->from('kiosk_config_item as kci')
            ->join('config_item', 'config_item.id = kci.config_item_id')
            ->where('kci.kiosk_id', $kiosk_id)
            ->where('kci.status', 'Queued')
            ->get();

        $data1 = $query1->result();
        foreach($data1 as $key => $d)
        {
            $query2 = $this->db
                ->where('config_item_id', $d->config_item_id)
                ->where('kiosk_id', $kiosk_id)
                ->where('status', 'Active')
                ->get('kiosk_config_item');

            if ($query2 && $query2->num_rows())
            {
                $active = $query2->row();
                $data1[$key]->current_value = $active->value;
                $data1[$key]->current_id = $active->id;
            }
            else
            {
                $data1[$key]->current_value = "";
                $data1[$key]->current_id = 0;
            }
        }

        return count($data1) ? $data1 : [];
    }

    public function getKioskConfigValues($kiosk_id)
    {
        $kiosk_id = intval($kiosk_id);
        $query = $this->db->select('config_item.id')->select('config_item.name')->select('config_item.field_type')->select('config_item.value_options')->select('kcia.value as active_value')->select('kcia.id as active_config_id')->join('kiosk_config_item kcia', "config_item.id = kcia.config_item_id AND kcia.status = 'Active' AND kcia.kiosk_id = $kiosk_id")->where('config_item.category_id', 5) // display
        ->get('config_item');
        return $query && $query->num_rows() ? $query->result() : [];
    }

    /**
     * function to get all kiosks based on filters in batch configurations page
     * @param array $conditions
     * @return array
     *
     */
    public function getKioskByCondition($conditions, $config_item)
    {
        $data2 = array();
        foreach($conditions as $condition)
        {
            if (isset($condition->state))
            {
                $state_cond = $condition->state;
            }

            if (isset($condition->model_name))
            {
                $model_cond = $condition->model_name;
            }

            if (isset($condition->site_category))
            {
                $category = $condition->site_category;
            }

            if (isset($condition->kiosk_number))
            {
                $kiosk_cond = $condition->kiosk_number;
            }

            if (isset($condition->config_item_id))
            {
                $config_id = $condition->config_item_id;
            }

            if (isset($condition->configuration_value))
            {
                $config_values = $condition->configuration_value;
            }
        }

        $this->db->select('k.id,k.number,k.status,kd.location_id,l.name as location_name,s.state,s.category,kd.status as deployment_status');
        $this->db->from(self::TABLE . ' as k');
        $this->db->join('kiosk_deployment as kd', 'kd.machine_id = k.id');
        $this->db->join('kiosk_location as l', 'l.id = kd.location_id', 'LEFT');
        $this->db->join('kiosk_model as km', 'km.id = k.kiosk_model_id', 'LEFT');
        $this->db->join('site as s', 's.id = l.site_id', 'LEFT');

        if (isset($state_cond))
        {
            $comma_separated = join("','", $state_cond);
            $this->db->where("s.state IN ('$comma_separated')");
        }

        if (isset($model_cond))
        {
            $models = join("','", $model_cond);
            $this->db->where("km.id IN ('$models')");
        }

        if (isset($kiosk_cond))
        {
            $kiosks = join("','", $kiosk_cond);
            $this->db->where("k.number IN ('$kiosks')");
        }

        if (isset($category))
        {
            $categories = join("','", $category);
            $this->db->where("s.category IN ('$categories')");
        }

        if (isset($config_values))
        {
            $config_value = join("','", $config_values);
        }

        $this->db->where('k.status', 'Active');
        $this->db->where("(kd.status LIKE '%Installed%' OR kd.status LIKE '%Removal Scheduled%')");
        $this->db->order_by('k.id');
        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            $data = $query->result();

            foreach($data as $new_value)
            {
                $new_value->config_item_id = '';
                $new_value->configuration_name = $config_item;
                $new_value->value = '';
                $this->db->select('*');
                $this->db->from('kiosk_config_item as kci');
                $this->db->where(array(
                    'kci.kiosk_id' => $new_value->id,
                    'kci.config_item_id' => $config_id,
                    'status' => 'Active'
                ));
                $query1 = $this->db->get();
                $data1 = $query1->result();
                $this->db->select('*');
                $this->db->from('kiosk_config_item as kci');
                $this->db->where(array(
                    'kci.kiosk_id' => $new_value->id,
                    'kci.config_item_id' => $config_id,
                    'status' => 'Queued'
                ));
                $KID = $new_value->id;
                $query2 = $this->db->get();
                $data2 = $query2->result();
                if (sizeof($data1) > 0)
                {
                    $new_value->value = $data1[0]->value;
                }

                if (sizeof($data2) > 0)
                {
                    if ($data2[0]->status == 'Queued')
                    {
                        $new_value->new_value = $data2[0]->value;
                    }

                    // Adding the value for status="Queued" to the object with the property new_value
                }
                else
                {
                    $new_value->new_value = '';
                }

                $data_new[] = $new_value;
            }

            return $data_new;
        }
        else
        {
            return false;
        }
    }

    /**
     * function to delete all configs in kiosk_config_item with status_queued =1 and update the row with status_queued = 1 and with value for value_queued
     * @param array $kiosks
     * return bool
     *
     */
    public function queueKiosks($kiosks)
    {
        $datetime = $kiosks['datetime'];
        unset($kiosks['datetime']);
        $deleted = 0;
        $values_queued = array();
        foreach($kiosks as $kiosk)
        {
            $value = $kiosk[0];
            $config_id = $kiosk[1];
            $kiosk_id = $kiosk[2];
            $this->db->where(array(
                'kiosk_id' => $kiosk_id,
                'config_item_id' => $config_id,
                'status' => 'Queued'
            ));
            $this->db->delete(self::CONFIGTABLE);
            $deleted = $deleted + $this->db->affected_rows();
            $now = new DateTime;
            $data = array(
                'kiosk_id' => $kiosk_id,
                'config_item_id' => $config_id,
                'value' => $value,
                'status' => 'Queued',
                'status_queued' => 0,
                'date_queued' => $datetime,
                'date_last_updated' => $datetime,
                'user_queued' => $this->session->userdata('name')
            );
            $values_queued[] = $data['value'];
            $this->db->insert(self::CONFIGTABLE, $data);
        }

        $deleted_rows = $this->db->affected_rows();
        if (sizeof($values_queued) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * function to deleted all kiosk with status = Queued
     * @param array $kiosks
     * return bool
     *
     */
    public function unqueueKiosks($kiosks)
    {
        $deleted = 0;
        foreach($kiosks as $kiosk)
        {
            if (array_key_exists(2, $kiosk))
            {
                $value = $kiosk[0];
                $config_id = $kiosk[1];
                $kiosk_id = $kiosk[2];
                $this->db->where(array(
                    'kiosk_id' => $kiosk_id,
                    'config_item_id' => $config_id,
                    'status' => 'Queued'
                ));
                $this->db->delete(self::CONFIGTABLE);
                $deleted = $deleted + $this->db->affected_rows();
            }
        }

        if ($deleted > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function commitFromPick($queued_id, $current_id, $datetime)
    {
        $updated_rows = 0;
        if ($queued_id > 0)
        {
            $this->db->where(array(
                'id' => $current_id
            ));
            $this->db->delete(self::CONFIGTABLE);
        }

        $data = array(
            'status' => 'Active',
            'date_applied' => $datetime,
            'date_last_updated' => $datetime,
            'user_applied' => $this->session->userdata('name')
        );
        $updated_rows = $this->db->update(self::CONFIGTABLE, $data, array(
            'id' => $queued_id
        ));
        if ($updated_rows < 0)
        {
            return false;
        }

        return true;
    }

    /**
     * function to change status of comittied Kiosks from Queued to Active
     * change status of Kiosks with status Acive to Inactive
     * @param array $kiosks
     *
     */
    public function commitSelectedKiosks($kiosks, $time, $datetime)
    {
        $updated_rows = 0;
        $kiosks = array_unique($kiosks, SORT_REGULAR);
        $now = new DateTime;
        foreach($kiosks as $kiosk)
        {
            $this->db->update(self::CONFIGTABLE, array(
                'status' => 'Inactive',
                'date_unapplied' => $time,
                'user_unapplied' => $this->session->userdata('name')
            ) , array(
                'kiosk_id' => $kiosk[2],
                'config_item_id' => $kiosk[1],
                'status' => 'Active'
            ));
            $data = array(
                'status' => 'Active',
                'date_applied' => $time,
                'date_last_updated' => $datetime,
                'user_applied' => $this->session->userdata('name')
            );
            $this->db->update(self::CONFIGTABLE, $data, array(
                'kiosk_id' => $kiosk[2],
                'config_item_id' => $kiosk[1],
                'status' => 'Queued'
            ));
            $updated_rows = $updated_rows + $this->db->affected_rows();
        }

        if ($updated_rows < 0)
        {
            return false;
        }

        return true;
    }

    public function getConfigHistoryPick($kiosk_id)
    {
        $query = $this->db->query("
SELECT `k`.`id`, `k`.`number`, `k`.`status`, `kd`.`location_id`, `l`.`name` as `location_name`, `s`.`state`, `s`.`category`, `kd`.`status` as `deployment_status` FROM `kiosk` as `k` JOIN `kiosk_deployment` as `kd` ON `kd`.`machine_id` = `k`.`id` LEFT JOIN `kiosk_location` as `l` ON `l`.`id` = `kd`.`location_id` LEFT JOIN `kiosk_model` as `km` ON `km`.`id` = `k`.`kiosk_model_id` LEFT JOIN `site` as `s` ON `s`.`id` = `l`.`site_id` WHERE `k`.`id` IN ('$kiosk_id') AND `k`.`status` = 'Queued' AND (kd.status LIKE '%Installed%' OR kd.status LIKE '%Removal Scheduled%') ORDER BY `k`.`id`
        ");
        if ($query->num_rows() > 0)
        {
            $data = $query->result();
            return $data;
        }
        else
        {
            return false;
        }
    }

    public function getActivelyDeployedKiosks()
    {
        $this->db->select('k.id,k.number,k.status,kd.location_id,l.name as location_name,s.state,s.category,c.id as config_item_id,c.name as configuration_name,c.unit_of_measure as uom,kc.value,kc.date_last_updated as last_updated,kc.status as configuration_status,kc.date_applied as startdate,kc.date_unapplied as enddate,kd.status as deployment_status');
        $this->db->from(self::TABLE . ' as k');
        $this->db->join('kiosk_deployment as kd', 'kd.machine_id = k.id');
        $this->db->join('kiosk_location as l', 'l.id = kd.location_id', 'LEFT');
        $this->db->join('kiosk_model as km', 'km.id = k.kiosk_model_id', 'LEFT');
        $this->db->join('kiosk_config_item as kc', 'kc.kiosk_id= k.id', 'LEFT');
        $this->db->join('config_item as c', 'c.id = kc.config_item_id', 'LEFT');
        $this->db->join('site as s', 's.id = l.site_id', 'LEFT');
        $this->db->where('k.status', 'Active');
        $this->db->where("(kd.status LIKE '%Installed%' OR kd.status LIKE '%Removal Scheduled%')");
        $this->db->order_by('kc.date_last_updated desc');
        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            $data = $query->result();

            return $data;
        }

        return false;
    }

    public function getHistory($conds)
    {
        $this->db->select('k.id,k.number,k.status,kd.location_id,l.name as location_name,s.state,s.category,c.id as config_item_id,c.name as configuration_name,c.unit_of_measure as uom,kc.value,kc.status as configuration_status,kc.value,kc.date_last_updated as last_updated,kc.date_applied as startdate,kc.date_unapplied as enddate,kd.status as deployment_status');
        $this->db->from(self::TABLE . ' as k');
        $this->db->join('kiosk_deployment as kd', 'kd.machine_id = k.id', 'LEFT');
        $this->db->join('kiosk_location as l', 'l.id = kd.location_id', 'LEFT');
        $this->db->join('kiosk_model as km', 'km.id = k.kiosk_model_id', 'LEFT');
        $this->db->join('kiosk_config_item as kc', 'kc.kiosk_id= k.id', 'LEFT');
        $this->db->join('config_item as c', 'c.id = kc.config_item_id', 'LEFT');
        $this->db->join('site as s', 's.id = l.site_id', 'LEFT');

        if (isset($conds['filter_kiosk']))
        {
            if (sizeof($conds['filter_kiosk']) > 0)
            {
                if (!empty($conds['filter_kiosk'][0]))
                {
                    $kiosks = join("','", $conds['filter_kiosk']);
                    $this->db->where("k.number IN ('$kiosks')");
                }
            }
        }

        if (isset($conds['filter_model']))
        {
            if (sizeof($conds['filter_model']) > 0)
            {
                if (!empty($conds['filter_model'][0]))
                {
                    $models = join("','", $conds['filter_model']);
                    $this->db->where("km.id IN ('$models')");
                }
            }
        }

        if (isset($conds['filter_historyCategory']))
        {
            if (sizeof($conds['filter_historyCategory']) > 0)
            {
                if (!empty($conds['filter_historyCategory'][0]))
                {
                    $categories = join("','", $conds['filter_historyCategory']);
                    $this->db->where("s.category IN ('$categories')");
                }
            }
        }

        if (isset($conds['filter_historystate']))
        {
            if (sizeof($conds['filter_historystate']) > 0)
            {
                if (!empty($conds['filter_historystate'][0]))
                {
                    $states = join("','", $conds['filter_historystate']);
                    $this->db->where("s.state IN ('$states')");
                }
            }
        }

        if (isset($conds['filter_historyItem']) && (!empty($conds['filter_historyItem'])))
        {
            $items = join("','", $conds['filter_historyItem']);
            $this->db->where("kc.config_item_id IN (' $items')");
        }

        if (isset($conds['filter_historyitemvalue']))
        {
            if (sizeof($conds['filter_historyitemvalue']) > 0)
            {
                if (!empty($conds['filter_historyitemvalue'][0]))
                {
                    $values = join("','", $conds['filter_historyitemvalue']);
                    $this->db->where("kc.value IN ('$values')");
                }
            }
        }

        if (isset($conds['filter_historystatus']))
        {
            if (sizeof($conds['filter_historystatus']) > 0)
            {
                if (!empty($conds['filter_historystatus'][0]))
                {
                    $status = join("','", $conds['filter_historystatus']);
                    $this->db->where("kc.status IN ('$status')");
                }
            }
        }

        $this->db->where('k.status', 'Active');
        $this->db->where("(kd.status LIKE '%Installed%' OR kd.status LIKE '%Removal Scheduled%')");
        $this->db->order_by('k.id');
        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            $data = $query->result();
            return $data;
        }

        return false;
    }
}