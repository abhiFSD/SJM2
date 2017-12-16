
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Schedule extends MY_Controller {
    
	public function __construct()
	{
		parent::__construct();
		$this->acl->hasAccess();
	}
    
    // to create new page
    // Step 1: create a function like line number: 11
    // Step 2: Copy page the content of the function and change the content view between header and footer.
    // Step 3: Create a view file in corresponding folder and update the respective path here.
    public function addtask()
    {

        $this->load->view("templates/header.php");
        
        // update views/systemadmin/addtask.php files        
        $this->load->view("schedule/addtask");
        
        $this->load->view("templates/footer.php");
    }
    
    public function viewassignedtasks()
    {
    
        $this->load->view("templates/header.php");
    
        // update views/systemadmin/addtask.php files
        $this->load->view("schedule/viewassignedtasks");
    
        $this->load->view("templates/footer.php");
    }
    
    public function tasktracker()
    {
    
    	$this->load->view("templates/header.php");
    
    	// update views/systemadmin/addtask.php files
    	$this->load->view("schedule/tasktracker");
    
    	$this->load->view("templates/footer.php");
    }
}