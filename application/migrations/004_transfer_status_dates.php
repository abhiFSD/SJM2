<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_transfer_status_dates extends CI_Migration
{
    public function up()
    {
        $queries[] = "ALTER TABLE `transfer_status`
            CHANGE `date_created` `date_created` TIMESTAMP NOT NULL DEFAULT 0";
        $queries[] = "ALTER TABLE `transfer`
            CHANGE `date_created` `date_created` TIMESTAMP NOT NULL DEFAULT 0,
            CHANGE `date_updated` `date_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        
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
