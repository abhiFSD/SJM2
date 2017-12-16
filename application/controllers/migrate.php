<?php defined("BASEPATH") or exit("No direct script access allowed");
  /**
   * The controller for the DB Migrations
   *
   * @author: Avtar Gaur
   * Description: This controller calls the DB migration files and keeps the database updated.
   * We can also call specific version of Migration
   * Date: Dec 17, 2016
   */
class Migrate extends MY_Controller{
  
  // calling the latest migration
  public function index(){    
    $this->load->library('migration');

    if ( ! $this->migration->current()){
      show_error($this->migration->error_string());
    }else{
      echo "<h1>Migrated to the latest version.</h1>";
    }

  }
  
  // calling a specific migration version  
  public function version($version) {
    // allowing this only to authorised users
    if($this->session->userdata('email_address') == "" ) {
             die('You are not authorised to do this operation.');
    }

    $this->load->library('migration');
        
    if ( $this->migration->version($version)){
      show_error($this->migration->error_string());
    }else{
        echo "<h1>Migrated to the version $version.</h1>";
    }
  }
}
?>