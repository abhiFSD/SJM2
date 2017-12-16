<?php
class Dex_model extends CI_Model
{
    const TABLE = 'dex_stock_movement_log';
    const OAA_TABLE = 'offering_attribute_allocation';
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    /**
     *
     * Get  previous onhand by position
     *
     * @param integer $id
     * @return int
     */
    public function getPrevOnHand($kid, $position)
    {
        $this->db
        ->select('oaa.value')
        ->from(self::OAA_TABLE . ' as oaa')
        ->where('oaa.kiosk_id', $kid)
        ->where('oaa.position', $position)
        ->where('oaa.offering_attribute_id', 4)
        ->where('oaa.status', 'Active');

        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $data = $query->result_array();
            return $data[0];
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * Update offering price if there is any discrepencies
     *
     * @param integer $id
     * @return string
     */
    public function UpdateOfferingPrice($kid, $position, $price)
    {

        $this->db
        ->select('id')
        ->select('position')
        ->select('offering_attribute_id')
        ->select('value')
        ->select('kiosk_id')
        ->select('status')
        ->select('date_queued')
        ->select('date_applied')
        ->select('date_unapplied')
        ->select('user_queued')
        ->select('user_applied')
        ->select('user_unapplied')
        ->from(self::OAA_TABLE . ' as oaa')
        ->where('oaa.kiosk_id', $kid)
        ->where('oaa.position', $position)
        ->where('oaa.offering_attribute_id', 3)
        ->where('oaa.status', 'Active');

        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            $data = $query->result_array();
            if ($data[0]['value'] != $price)
            {
                $cid = $data[0]['id'];
                unset($data[0]['id']);
                unset($data[0]['isOptional']);
                unset($data[0]['queue_type']);
                unset($data[0]['commit_type']);
                unset($data[0]['user_queued']);
                unset($data[0]['date_queued']);
                unset($data[0]['date_queued']);
                unset($data[0]['date_applied']);
                $data[0]['status'] = "Inactive";
                $new['position'] = $position;
                $new['offering_attribute_id'] = 3;
                $new['value'] = $price;
                $new['kiosk_id'] = $kid;
                $new['status'] = "Active";
                $new['date_applied'] = date('Y-m-d H:i:s');
                $new['user_applied'] = "DEX Read";
                $data[0]['date_unapplied'] = date('Y-m-d H:i:s');
                $data[0]['user_unapplied'] = "DEX Read";
                //   $this->db->delete('offering_attribute_allocation',  array('id'=>$data[0]['id']));
                $this->db->insert('offering_change_log', $data[0]);
                $offering_id = $this->db->update('offering_attribute_allocation', $new, array(
                    'id' => $cid
                ));
            }
            return $data[0];
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * Get kiosk id by number
     *
     * @param integer $id
     * @return string
     */
    public function getKioskId($kioskNumber)
    {
        $this->db
        ->select('id')
        ->from('kiosk')
        ->where('number', $kioskNumber);
        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            $data = $query->result_array();
            return $data[0];
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * 
     *
     * @param integer $id
     * @return string
     */
    public function getSKUFromOfferingAttribute($kid, $position)
    {
        $this->db
        ->select('oaa.value')
        ->from(self::OAA_TABLE . ' as oaa')
        ->where('oaa.kiosk_id', $kid)
        ->where('oaa.position', $position)
        ->where('oaa.offering_attribute_id', 1)
        ->where('oaa.status', 'Active');

        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            $data = $query->result_array();
            return $data[0];
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * 
     *
     * @param integer $id
     * @return string
     */
    public function getSKUFromTransferItem($kid, $position)
    {
        $this->db
        ->select('*')
        ->from('offering_attribute_allocation')
        ->join('kiosk', 'kiosk.id = offering_attribute_allocation.kiosk_id')
        ->where('kiosk.number', $kid)
        ->where('offering_attribute_allocation.position', $position)
        ->where('offering_attribute_allocation.offering_attribute_id', 4)
        ->where('offering_attribute_allocation.status', 'Active');

        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            $data = $query->result_array();
            return $data[0];
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * Get the dex onhand by position
     *
     * @param integer $id
     * @return string
     */
    public function getOnand($position, $kid)
    {
        $this->db
        ->select('oaa.value')
        ->select('oaa.kiosk_id')
        ->from(self::OAA_TABLE . ' as oaa')
        ->where('oaa.kiosk_id', $kid)
        ->where('oaa.position', $position)
        ->where('oaa.offering_attribute_id', 4)
        ->where('oaa.status', 'Active');

        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            $data = $query->result_array();
            return $data[0];
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * Update onhand by position
     *
     * @param integer $id
     * @return string
     */
    public function setOnand($value, $kid, $position)
    {
        $this->db->update(self::OAA_TABLE, array(
            'value' => $value
        ) , array(
            'kiosk_id' => $kid,
            'position' => $position,
            'offering_attribute_id' => 4
        ));
    }

    /**
     *
     * Get the dex movement by position
     *
     * @param integer $id
     * @return string
     */
    public function getByPosition($id, $kid, $datetime)
    {
        $data = [];

        $this->db
        ->select('total_sales')
        ->select('date_created')
        ->from('dex_stock_movement_log')
        ->where('kiosk_id', $kid)
        ->where('position', $id)
        ->order_by('id', 'desc');
        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {
            $data = $query->result_array();
            return $data[0];
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * Get the dex movement by position
     *
     * @param integer $id
     * @return string
     */
    public function saveDexMovement($inputs, $type, $did)
    {
        $this->load->model('Offeringattributeallocationmodel');
        foreach($inputs as $key => $input)
        {
            if ($input['position'] > 0)
            {
                $out = self::getByPosition($input['position'], $input['kiosk_id'], $input['datetime']);
            }
            else
            {
                continue;
            }
            $onhand = self::getOnand($input['position'], $input['kiosk_id']);
            $kid = $onhand['kiosk_id'];
            $movement = 0;
            if (isset($out['total_sales']))
            {
                $movement = $input['total_sales'] - $out['total_sales'];
            }
            else
            {
                $movement = $input['total_sales'] - 0;
            }
            if ($movement > 0)
            {
                $onhand = $onhand['value'] - $movement;
                // Its being updated by TXN SQS
                $data = array(
                    'kiosk_id' => $input['kiosk_id'],
                    'position' => $input['position'],
                    'total_sales' => $input['total_sales'],
                    'movement' => $movement,
                    'new_SOH' => $onhand,
                    'dex_data_id' => $did,
                    'date_created' => $input['datetime']
                );
                $this->db->insert(self::TABLE, $data);
                $sku = self::getSKUFromOfferingAttribute($input['kiosk_id'], $input['position']);
            }
        }
    }

}
