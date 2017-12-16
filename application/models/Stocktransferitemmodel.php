<?php
/**
 * Manage the stock transfer items here
 * @author pbpilai
 *
 */
class StocktransferitemModel extends CI_Model
{
	
	const TABLE = 'stock_transfer_item';
	
	
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
	 * Get the items based on the stock transfer ID
	 * 
	 * @param integer $transferId
	 * @return array|boolean
	 */
	public function getByTransferId($transferId)
	{
		$query = $this->db->get_where(self::TABLE, array('transfer_id' => $transferId));
	
		if ($query->num_rows() > 0) {
			$data = $query->result();				
			return $data;
		} else {
			return false;
		}
	}
	
	public function getByTransferIdDetails($transferId)
	{
		$this->db->select(' s.id, s.sku_value, s.name, sti.item_quantity ');
		$this->db->from('stock_transfer_item as sti');
		$this->db->join('sku as s', 's.id = sti.sku_id');
		$this->db->where('transfer_id', $transferId);
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			$data = $query->result();
			return $data;
		} else {
			return false;
		}
	}
	
	
	
	
	
}