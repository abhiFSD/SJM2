<?php
class InventorylocationModel extends CI_Model
{
	
	const TABLE = 'inventory_location';
	
	
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
		}
	}
	
	/**
	 * Get the details of the location
	 * 
	 * @param unknown $id
	 * 
	 */
	public function getDetails($id)
	{
  	    $query = $this->db->get_where(self::TABLE, array('id' => $id));
  	 
  	    if ($query->num_rows() > 0) {
  	      $data = $query->result();

  	      /*
  	      if ($data[0]->type == 'Warehouse') {
  	         return $this->getWarehouse($id);
  	      } else {
  	        return false;
  	      } */
  	      return $data;
	    } else {
	        return false;
	    }
	}
	
	/**
	 * Get specific warehouse
	 * 
	 * @param $id
	 * @return array|boolean
	 */
	public function getWarehouse($id)
	{
	
	  $this->db->select('il.id as id, w.name as name');
	  $this->db->from(self::TABLE . ' as il');
	  $this->db->join('warehouse as w','w.id=il.warehouse_id');
	  $this->db->where('w.id = '. $id);
	  $query = $this->db->get();
	
	  if ($query->num_rows() > 0) {
	    $data = $query->result();
	    return $data;
	  } else {
	    return false;
	  }
	}
	
	
	
	/**
	 * Get all the warehouses
	 * @return array|boolean
	 */
	public function findWarehouses()
	{
	    $this->db->order_by('name');
		
		$query = $this->db->get(self::TABLE);
		
		if ($query->num_rows() > 0) {
			$data = $query->result();
			return $data;
		} else {
			return false;
		}
	}
	
	/**
	 * Get all the supplier
	 * @return array|boolean
	 */
	public function findSuppliers()
	{
	
		$this->db->select('il.id as id, w.name as name');
		$this->db->from(self::TABLE . 'as il');
		$this->db->join('supplier as w','w.id=il.warehouse_id');
		$query = $this->db->get();
	
		if ($query->num_rows() > 0) {
			$data = $query->result();
			return $data;
		} else {
			return false;
		}
	}
}