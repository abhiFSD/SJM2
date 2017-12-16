<?php

class StocktransferModel extends CI_Model
{
	
	const TABLE = 'stock_transfer';
	
	
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
	
	
	
	
}