<?php

class PartyModel extends CI_Model
{

    const TABLE = 'party';

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


    /**
     * Get all the details based on the deployment
     *
     * @return array
     */
    public function getAll($status = 'Installed')
    {
        $this->db->select(' d.id, m.id, m.number, kl.name');
        $this->db->from(self::TABLE. ' as d');
        $this->db->join('machine as m', 'm.number = d.machine_id');
        $this->db->join('kiosk_location as kl', 'kl.location_id = d.location_id');
        if ($status != "") {
            $this->db->where("d.status = '".$status."'");
        }
        $this->db->order_by('m.number');
        $query = $this->db->get();

        $results = $query->result();

        return $results;
    }

    public function getLicensorsNew ($criteria = array())
    {
        $this->db->select('*');
        $this->db->from('licensor');
        $this->db->where('status','Active');
        $query = $this->db->get();

        $results = $query->result();

        return $results;
    }

    public function getLicensors ($criteria = array())
    {
        $query = $this->db
            ->select('party_type_allocation.id')
            ->select('party.org_name')
            ->select('party.display_name')
            ->select('party_type_allocation.party_id')
            ->join('party_type_allocation', 'party_type_allocation.party_id = party.id')
            ->where('party_type_allocation.party_type_id', 2)
            ->order_by('party.display_name', 'asc')
            ->get('party');

        return $query && $query->num_rows() ? $query->result() : [];
    }

    public function getOrganisations ($criteria = array())
    {
        $this->db->select('p.org_name, p.display_name');
        $this->db->from(self::TABLE. ' as p');
        $this->db->join('party_type_allocation as pa', 'p.id = pa.party_id');
        $this->db->where("pa.party_type_id = 1");
        $query = $this->db->get();

        $results = $query->result();

        return $results;
    }

    public function getnextId()
    {
        $lastId = $this->db->select('id')->order_by('id','desc')->limit(1)->get('party')->row('id');
        $lastId++;
        return $lastId;
    }

    /*
     * Function: getProductOwners()
     * Description: This will get all the Product Owners list from the party table
     *
     * @param: NULL
     * Return Value: $data => in pair("field" => "value")
     * @author: Avtar Gaur(developer.avtargaur@gmail.com)
     * Date: Dec 21, 2016
     */
    public function getProductOwners()
    {
        $this->db->select('p.display_name, pa.id');
        $this->db->from(self::TABLE. ' as p');
        $this->db->join('party_type_allocation as pa', 'p.id = pa.party_id');
        $this->db->where("pa.party_type_id = 8");
        $query = $this->db->get();

        $results = $query->result_array();

        return $results;
    }

    public function addNewParty($data)
    {
        $this->db->insert('party', $data);

        return $this->db->insert_id();
    }

    public function addAsLicensor($party_id)
    {
        $query = $this->db
            ->where('party_id', $party_id)
            ->get('party_type_allocation');

        if ($query && $query->num_rows())
        {
            return $query->row()->id;
        }
        else
        {
            $this->db
                ->set('party_id', $party_id)
                ->set('party_type_id', 2)
                ->set('user_role_id', 4)
                ->insert('party_type_allocation');

            return $this->db->insert_id();
        }
    }

}
