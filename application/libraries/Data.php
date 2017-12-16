<?php

class Data
{
	public $ci;
	
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->database();
	}
	
	public function fetch($table, $fields = array(), $criteria = null, $orderby = null)
	{
		
		$sql ="";
		
		if (is_array($fields) && count($fields) > 0 ) {
			$fieldStr = implode(",", $fields);
		} else {
			$fieldStr = "*";
		}
		
		$sql  = "select {$fieldStr} from {$table} ";
		
		if ($criteria != "") {
			$sql .= "where " .$criteria;
		}
		if ($orderby != null) {
		    $sql .= " order by ". $orderby;
		}
	
		$query = $this->ci->db->query($sql);
		return $query->result_array();
		
	}
	
	public function insert($table, $data)
	{
		$this->ci->db->insert($table, $data);
		return $this->ci->db->insert_id();
	}
	
	public function update($table, $data, $criteria)
	{
		$this->ci->db->update($table, $data, $criteria);
		
	}
	
}