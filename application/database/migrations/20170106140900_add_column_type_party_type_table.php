<?php defined('BASEPATH') OR exit('No direct script access allowed');

  /*
   * @author: Annie
   * Description: Adding the column "type" to "party_type" table
   *
   *
   * Date: Dec 26, 2016
   */
class Migration_add_column_type_party_type_table extends CI_Migration {

	public function up(){
        // adding column "type" to the "party_type" table
		$fields = array(
        	'type' => array(
                'type' => 'varchar',
                'constraint' => '45',
                'null' => 'yes'
            )
        );
		$this->dbforge->add_column("party_type", $fields);
	}

	public function down(){
        // dropping column "party_type_allocation_id" from "SKU" table
        $this->dbforge->drop_column("party_type", 'party_type');
	}
}
