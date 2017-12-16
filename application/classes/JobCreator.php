<?php

namespace POW\Classes;

class JobCreator
{
    public static function run($post)
    {
        $user_id = get_instance()->session->userdata('user_id');
        $transfer_count = 0;

        // expecting to create multiple jobs
        // at this point only warehouse -> kiosk and kios -> warehouse jobs can bre created
        if ($post['location_from_type'] == 'kiosk' || $post['location_to_type'] == 'kiosk')
        {
            if ($post['location_from_type'] == 'inventory_location' || $post['location_to_type'] == 'inventory_location')
            {
                if ($post['location_from_type'] == 'inventory_location')
                {
                    $loop_name = 'to_kiosk_id';
                    $non_loop_attribute = 'location_from_id';
                    $non_loop_id = $post['from_inventory_location'];
                    $loop_attribute = 'location_to_id';
                }
                elseif ($post['location_to_type'] == 'inventory_location')
                {
                    $loop_name = 'from_kiosk_id';
                    $non_loop_attribute = 'location_to_id';
                    $non_loop_id = $post['to_inventory_location'];
                    $loop_attribute = 'location_from_id';
                }

                if (!empty($post[$loop_name]))
                {
                    foreach ($post[$loop_name] as $kiosk_id)
                    {
                        $transfer = new \POW\Transfer();
                        $transfer->location_from_type = $post['location_from_type'];
                        $transfer->location_to_type = $post['location_to_type'];
                        $transfer->{$non_loop_attribute} = $non_loop_id;
                        $transfer->{$loop_attribute} = $kiosk_id;
                        $transfer->status = 'Job Created';
                        $transfer->submitter = $user_id;
                        $transfer->date_created = date('Y-m-d H:i:s');
                        $transfer->notes = $post['notes'];
                        $transfer->save();

                        $transfer_status = new \POW\TransferStatus();
                        $transfer_status->date_created = date('Y-m-d H:i:s');
                        $transfer_status->transfer_id = $transfer->id;
                        $transfer_status->status = 'Job Created';
                        $transfer_status->location_type = $transfer->location_from_type;
                        $transfer_status->location_id = $transfer->location_from_id;
                        $transfer_status->user_id = $user_id;
                        $transfer_status->save();

                        $transfer_count++;
                    }
                }
            }
        }

        return $transfer_count;
    }
}
