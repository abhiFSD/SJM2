<?php

class DeploymentModel extends CI_Model
{

	const TABLE = 'kiosk_deployment';

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
		$this->db->join('kiosk as m', 'm.id = d.machine_id');
		$this->db->join('kiosk_location as kl', 'kl.id = d.location_id');
		if ($status != "") {
		    $this->db->where("d.status = '".$status."'");
		}
		$this->db->order_by('m.id');
		$query = $this->db->get();

		$results = $query->result();

		return $results;
	}

	public function getDeploymentByDate($machine, $checkDate)
	{

	    $this->db->where('machine_id', $machine);
	    $this->db->where('installed_date <=', $checkDate->format('Y-m-d H:i:s'));

	    $query = $this->db->get(self::TABLE);

	    foreach($query->result() as $result) {


	        $startDate = new DateTime($result->installed_date);

	        $endDateStr = ($result->uninstalled_date == '0000-00-00 00:00:00' ? date('Y-m-d H:i:s'):$result->uninstalled_date);

	        $endDate = new DateTime($endDateStr);

	        if ($checkDate >= $startDate && $checkDate <= $endDate) {
	            return $result->deployment_id;
	        }
	    }

	}

}
