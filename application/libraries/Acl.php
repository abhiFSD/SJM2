<?php
/**
 * Library to deal with the permission for the user
 * @author pbpilai
 *
 */
class Acl
{
	public $ci;
	
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->database();
	}
	
	/**
	 * Check wheher the user have logged in
	 * @return boolean
	 */
	public function isValidUser()
	{
		if ($this->ci->session->userdata('email_address') == "" ) {
			redirect('/auth/login');
		} else {
			return true;
		}
	}
	
	/**
	 * Function to check for right access for to the users
	 * 
	 * @param string $module
	 * @param string $action
	 * @return boolean
	 */
	public function hasAccess($module = null, $action = "")
	{
		if ($this->isValidUser()) {
			//@todo set the permission channel
			return true;
		} else {
			
		}
	}
	
}