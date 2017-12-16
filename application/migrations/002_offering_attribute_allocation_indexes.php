<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_offering_attribute_allocation_indexes extends CI_Migration
{
    public function up()
    {
        $queries[] = 'ALTER TABLE `offering_attribute_allocation`
            DROP INDEX `attribute`,
            ADD INDEX `index_offering_attribute_allocation_kps` (`kiosk_id`, `position`, `status`),
            ADD INDEX `index_offering_attribute_allocation_kpsq` (`kiosk_id`, `position`, `status`, `queue_type`),
            ADD INDEX `index_offering_attribute_allocation_kpsc` (`kiosk_id`, `position`, `status`, `commit_type`)';

        foreach ($queries as $query)
        {
            if (!$this->db->query($query))
            {
                $error = $this->db->error();
                
                log_message('error', 'DB ERROR: '.$error['message']);
            }
        }
    }

    public function down() {}
}
