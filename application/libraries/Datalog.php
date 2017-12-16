<?php
/**
 * Library to deal with the Logs for the user
 * @author pbpilai
 *
 */
class Datalog
{
	public $ci;
	
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->database();
	}
	
	/**
	 * Add the log data into the database
	 * 
	 * @param array $data
	 */
	public function add(array $data)
	{
		
		$userId = $userId = $this->ci->session->userdata('user_id'); 
		
		$data['user_id'] = $userId;
		$data['date_time'] = date('Y-m-d H:i:s');
		
		
		$this->ci->db->insert('user_log', $data);
	}
	
	/**
	 *  Display the log entries
	 *  
	 * @param integer $id
	 */
	public function display($id)
	{
		$this->ci->db->select('user_log.id, user_log.action_details, user_log.date_time, user.display_name');
		$this->ci->db->from('user_log');
		$this->ci->db->join('user', 'user.id = user_log.user_id');
		$this->ci->db->where('user_log.id', $id);
		$query = $this->ci->db->get();
		
		$data = $query->result();
		
		$log = $data[0];
		
		
		$date = new DateTime($log->date_time);
		
		echo $log->action_details. " &nbsp;&nbsp;&nbsp;&nbsp;<small class='badge'> " . $log->display_name . " on ". $date->format('d/m/Y H:i:s'). '</small>';
		
		
		
	}
	

	
}