<?php

class Txnimportmodel extends CI_Model
{
	const TABLE = 'txn_import';

	public function is_duplicate($date_authorised, $kiosk_number)
	{
		$query = $this->db
			->where('date_authorised', $date_authorised)
			->where('kiosk_number', $kiosk_number)
			->get(self::TABLE);

		return $query && $query->num_rows() ? true : false;
	}

	public function insert($row)
	{
		return $this->db->insert(self::TABLE, $row);
	}

}
