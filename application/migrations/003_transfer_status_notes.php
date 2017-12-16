<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_transfer_status_notes extends CI_Migration
{
    public function up()
    {
        $queries[] = "ALTER TABLE `transfer_status`
            ADD COLUMN handover datetime DEFAULT NULL,
            ADD COLUMN note varchar(255) DEFAULT NULL,
            ADD COLUMN tracking_link text";
        $queries[] = "ALTER TABLE `transfer_item`
            ADD INDEX index_transfer_item_oai (`offering_attribute_id`)";
        $queries[] = "ALTER TABLE `item`
            ADD INDEX index_item_product_type (`product_type`)";
        
        foreach ($queries as $query)
        {
            if (!$this->db->query($query))
            {
                $error = $this->db->error();

                log_error('error', 'DB ERROR: '.$error['message']);
            }
        }
    }

    public function down() {}
}
