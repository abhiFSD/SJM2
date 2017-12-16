<?php defined('BASEPATH') OR exit('No direct script access allowed');

  /*
   *
   * Description: The Migration for removing store_id from kiosk table
   *
   *
   * Date: Jan 4, 2016
   */
class Migration_remove_store_id_kiosk_table extends CI_Migration {

  public function up(){
        // removing store_id from the kiosk table
        $this->dbforge->drop_column("kiosk", "store_id");

	}

	public function down(){
		$fields = array(
            'store_id' => array(
                'default' => NONE,
                'type' => 'INT',
                'null' => NO,
             )
        );
        // adding columns from the product_category_classification table
        $this->dbforge->add_column("kiosk", $fields);


	}
}
