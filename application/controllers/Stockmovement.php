<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stockmovement extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->acl->hasAccess();
        $this->load->library('data');

        $data['locationid'] = $this->input->get_post('location');
        $data['adjustment'] = $this->input->get_post('adjustment');
        $data['minamount'] = $this->input->get_post('minamount');
        $data['maxamount'] = $this->input->get_post('maxamount');
        $data['mindate'] = $this->input->get_post('mindate');
        $data['maxdate'] = $this->input->get_post('maxdate');
        $data['productname'] = $this->input->get_post('product');
        $data['kiosk'] = $this->input->get_post('kiosks');
        $data['location_type'] = $this->input->get_post('location_type');
        $data['description'] = $this->input->get_post('description');
        $data['item_category_type'] = $this->input->get_post('item_category_type');
        $data['datetime_3m'] = $this->input->get_post('datetime_3m');
        $data['user_id_filter'] = $this->input->get_post('user_id');

        $this->view_data = array_merge($this->view_data, $data);
    }

    private function load_job_filters()
    {
        foreach (POW\Site::get_states() as $state)
        {
            $states[$state] = $state;
        }

        $this->view_data['transfer_types'] = [
            'inventory_location' => 'Warehouse',
            'kiosk' => 'Kiosk',
        ];
        $this->view_data['location_types'] = [
            'inventory_location' => 'Warehouse',
            'kiosk' => 'Kiosk',
            'Staff' => 'Staff',
            'Freight Provider' => 'Freight Provider',
        ];
        $this->view_data['statuses'] = [
            'Job Created' => 'Job Created',
            'Pick generated' => 'Pick generated',
            'Partially Picked' => 'Partially Picked',
            'Fully picked' => 'Fully picked',
            'Transferred' => 'Transferred',
            'Partially allotted' => 'Partially allotted',
            'Fully allotted' => 'Fully allotted',
        ];
        $this->view_data['states'] = $states;
    }

    public function jobs()
    {
        $this->load->helper('my_url');

        $this->view_data['selected_filters'] = $this->session->userdata('current-jobs-filters');

        if ($post = $this->input->post())
        {
            $this->view_data['selected_filters'] = $post;
            $this->session->set_userdata('current-jobs-filters', $post);
        }

        $this->view_data['transfers'] = POW\Transfer::get('open', $this->view_data['selected_filters']);

        if ($post)
        {
            $this->load->view('stockmovement/sections/jobs', $this->view_data);
        }
        else
        {
            $this->load_job_filters();
            $this->view_data['body_id'] = 'current-jobs';
            $this->view_data['filters'] = $this->load->view('stockmovement/sections/jobs_filters', $this->view_data, true);

            $this->default_view('stockmovement/jobs');
        }
    }

    public function completed_jobs()
    {
        $this->load->helper('my_url');

        $this->view_data['selected_filters'] = $this->session->userdata('completed-jobs-filters');

        if ($post = $this->input->post())
        {
            $this->view_data['selected_filters'] = $post;
            $this->session->set_userdata('completed-jobs-filters', $post);
        }

        $this->view_data['transfers'] = POW\Transfer::get('closed', $this->view_data['selected_filters']);

        if ($post)
        {
            $this->view_data['completed_jobs_only'] = true;
            $this->load->view('stockmovement/sections/jobs', $this->view_data);
        }
        else
        {
            $this->load_job_filters();
            $this->view_data['body_id'] = 'completed-jobs';
            $this->view_data['filters'] = $this->load->view('stockmovement/sections/completed_jobs_filters', $this->view_data, true);

            $this->default_view('stockmovement/completed_jobs');
        }
    }

    public function create()
    {
        if ($post = $this->input->post())
        {
            $transfer_count = POW\Classes\JobCreator::run($post);
            $msg = new POW\Classes\Message();

            if ($transfer_count)
            {
                $msg->reload();
                $this->session->set_flashdata('success_message', $transfer_count.' '.plural($transfer_count, 'job', 'jobs').' created');
            }
            else
            {
                $msg->close_modal();
                $msg->exec('bootstrap_alert', ['danger', 'No jobs were created']);
            }

            return $msg->send();
        }

        foreach (POW\Site::get_states() as $state)
        {
            $states[$state] = $state;
        }

        $inventory_locations = [];
        foreach (POW\InventoryLocation::get_all(true) as $inventory_location)
        {
            $inventory_locations[$inventory_location->id] = $inventory_location->name;
        }

        $this->view_data['states'] = $states;
        $this->view_data['body_id'] = 'current-jobs';
        $this->view_data['location_types'] = [
            'inventory_location' => 'Warehouse',
            'kiosk' => 'Kiosk',
            'Staff' => 'Staff',
            'Freight Provider' => 'Freight Provider',
        ];
        $this->view_data['statuses'] = [
            'Job Created' => 'Job Created',
            'Pick generated' => 'Pick generated',
            'Partially Picked' => 'Partially Picked',
            'Fully picked' => 'Fully picked',
            'Transferred' => 'Transferred',
            'Partially allotted' => 'Partially allotted',
            'Fully allotted' => 'Fully allotted',
        ];
        $this->view_data['inventory_locations'] = $inventory_locations;
        $this->view_data['kiosks'] = POW\Kiosk::get_installed();

        $this->load->view('stockmovement/modals/create', $this->view_data);
    }

    public function delete_transfers()
    {
        if ($post = $this->input->post())
        {
            $transfers_deleted = [];
            $not_deleted = [];

            $msg = new POW\Classes\Message();
            $msg->exec('remove_backdrop');

            foreach ($post['transfer_ids'] as $transfer_id)
            {
                $transfer = POW\Transfer::with_id($transfer_id);
                $last_transfer_status = $transfer->last_transfer_status;

                // warehouse returns should be phased out soon
                if ($last_transfer_status->id == 0 || in_array(strtolower($last_transfer_status->status), [
                    'job created', 'pick generated', 'partially picked', 'warehouse returns'
                ]))
                {
                    $transfer_id = $transfer->id;
                    if ($transfer->delete())
                    {
                        $transfers_deleted[] = $transfer_id;
                    }
                }
                else {
                    $not_deleted[] = sprintf(
                        '%s - %s to %s',
                        $transfer->id,
                        $transfer->location_from_type == 'inventory_location' ? $transfer->location_from->name : $transfer->location_from->number,
                        $transfer->location_to_type == 'inventory_location' ? $transfer->location_to->name : $transfer->location_to->number
                    );
                }
            }

            if ($count = count($transfers_deleted))
            {
                $string = plural($count, 'Job', 'Jobs').' '.implode(', ', $transfers_deleted).' '.plural($count, 'was', 'were').' deleted';
                $msg->exec('bootstrap_alert', ['success', $string]);
            }
            if ($count = count($not_deleted))
            {
                $string = 'The following '.plural(count($count), 'job was not deleted because it has', 'jobs were not deleted because they have').' progressed too far: ';
                $msg->exec('bootstrap_alert', ['danger', $string.implode(', ', $not_deleted)]);
            }

            $msg->exec('submit_filter');
            $msg->send();
        }
    }

    public function filter_names($direction, $type = null)
    {
        $msg = new POW\Classes\Message();

        $html = '';
        if ($type == 'inventory_location')
        {
            $html = $this->load->view('templates/option_list/inventory_locations', ['inventory_locations' => POW\InventoryLocation::get_all()], true);
        }
        else if ($type == 'kiosk')
        {
            $html = $this->load->view('templates/option_list/kiosks', ['kiosks' => POW\Kiosk::get_installed()], true);
        }

        $selector = '#jobs-filters select[name=location_'.$direction.'_id\[\]]';

        $msg->exec('rewrite_filter_select', [$selector, $html]);
        $msg->send();
    }

    public function create_job_names($trigger_name)
    {
        if ($post = $this->input->post())
        {
            $msg = new POW\Classes\Message();
            $direction = substr($trigger_name, 0, strpos($trigger_name, '_'));

            $html = '';
            if ($post['location_'.$direction.'_type'] == 'inventory_location')
            {
                $html = $this->load->view('templates/option_list/inventory_locations', ['inventory_locations' => POW\InventoryLocation::get_all(true, $post[$trigger_name])], true);
                $selector = '#create-job select[name='.$direction.'_inventory_location]';

                $msg->html($selector, $html);
            }
            else if ($post['location_'.$direction.'_type'] == 'kiosk')
            {
                $options = new stdClass();
                $options->state = $post[$trigger_name];

                $html = $this->load->view('templates/option_list/kiosks_div', ['kiosks' => POW\Kiosk::get_installed($options)], true);
                $selector = '#create-job .'.$direction.'-kiosk .wrapper';

                $msg->html($selector, $html);
            }

            $msg->send();
        }
    }

    public function history($transfer_id)
    {
        $transfer = POW\Transfer::with_id($transfer_id);

        $this->view_data['transfer_statuses'] = $transfer->transfer_statuses;

        $this->load->view('stockmovement/modals/history', $this->view_data);
    }

    public function transfer()
    {
        if ($post = $this->input->post())
        {
            $transfers_updated = 0;
            $msg = new POW\Classes\Message();

            foreach ($post['transfer_ids'] as $transfer_id)
            {
                $transfer = POW\Transfer::with_id($transfer_id);

                if (!in_array(strtolower($transfer->last_transfer_status->status), ['fully picked', 'transferred'])) continue;

                $transfer->status = 'Transferred';
                $transfer->save();

                $transfer_status = new POW\TransferStatus();
                $transfer_status->date_created = date('Y-m-d H:i:s');
                $transfer_status->transfer_id = $transfer_id;
                $transfer_status->status = 'Transferred';
                $transfer_status->location_type = $post['location_type'];
                $transfer_status->note = $post['note'];
                $transfer_status->user_id = $this->session->userdata('user_id');
                $transfer_status->handover = $post['handover_date'].' '.$post['handover_time'].':00';

                if ($post['location_type'] == 'Freight Provider')
                {
                    if (!empty($post['freight_provider']))
                    {
                        $transfer_status->location_id = $post['freight_provider'];
                    }

                    $transfer_status->tracking_link = $post['tracking_link'];
                }
                if ($post['location_type'] == 'Staff')
                {
                    if (!empty($post['staff']))
                    {
                        $transfer_status->location_id = $post['staff'];
                    }
                }

                $transfer_status->save();

                $transfers_updated++;
            }

            if ($transfers_updated)
            {
                $msg->exec('bootstrap_alert', ['success', number_noun_verb($transfers_updated, 'job', 'jobs', 'updated')]);
                $msg->exec('submit_filter');
            }
            if ($not_updated = count($post['transfer_ids']) - $transfers_updated)
            {
                $msg->exec('bootstrap_alert', ['danger', number_noun_verb($not_updated, 'job', 'jobs', 'not updated')]);
            }

            $msg->close_modal();

            return $msg->send();
        }

        $freight_providers = [];
        $staff = [];

        $parties = POW\Party::freight_providers();
        foreach ($parties as $party)
        {
            $freight_providers[$party->id] = $party->display_name;
        }

        $parties = POW\Party::staff();
        foreach ($parties as $party)
        {
            $staff[$party->id] = $party->display_name;
        }

        $this->view_data['freight_providers'] = $freight_providers;
        $this->view_data['staff'] = $staff;

        $this->load->view('stockmovement/modals/transfer', $this->view_data);
    }

    /**
     * 
     * Get the list for the stock movement
     * @param string $csv
     */
    public function listall($cleandata = false)
    {
        $get = $this->input->get();
        $this->load->helper('utility');

        // force inventory location filter
        if (!empty($this->view_data['locationid']))
        {
            if (!empty($this->view_data['kiosk']))
            {
                unset($get['kiosks']);
            }
            
            if (!empty($this->view_data['kiosk']) || 'inventory_location' != $this->view_data['location_type'])
            {
                $get['location_type'] = 'inventory_location';
                
                redirect('/stockmovement/listall/?'.http_build_query($get));
            }
        }

        // force kiosk filter
        if (!empty($this->view_data['kiosk']))
        {
            if (!empty($this->view_data['locationid']))
            {
                unset($get['location']);
            }
            
            if (!empty($this->view_data['locationid']) || 'kiosk' != $this->view_data['location_type'])
            {
                $get['location_type'] = 'kiosk';
            
                redirect('/stockmovement/listall/?'.http_build_query($get));
            }
        }

        $this->view_data['mindate'] = $this->input->get_post('mindate') ? $this->input->get_post('mindate') : ($this->input->get_post('datetime_3m') ? '' : date('Y-m-01'));
        
        $this->load->model('Stockmovementlogmodel');

        list($query, $count) = $this->Stockmovementlogmodel->getData($this->view_data);
        $items = array();
        foreach ($query->result() as $result) 
        {
            if( $result->location_type =="kiosk")
                $FROM =$result->KFrom;
            else
                $FROM =$result->From;   

            $items[] = array(
                POW\Helpers\format_date($result->DateTime),
                $FROM,
                $result->SKU,
                $result->ProductName,
                $result->Amount,
                $result->SOH,
                $result->MovementType == "" ? "Dex Read" : $result->MovementType,
                $result->Description,
                $result->MovementBy,
                POW\Helpers\format_date($result->DateCreated)
            );
        }

        $skipped = $this->session->flashdata('skipped');

        if ($skipped)  {
            $this->view_data['skipped'] = $skipped;
        }

        $this->load->model('Kioskmodel');

        $this->view_data['kiosks'] = POW\Kiosk::get_installed(null);
        $this->view_data['products'] = POW\Sku::list_key_val('name', 'name', [['name', 'asc']]);
        $this->view_data['cleandata'] = $cleandata;
        $this->view_data['locations'] = POW\InventoryLocation::list_key_val('id', 'name', [['name', 'asc']]);
        $this->view_data['items'] = $items;

        $this->load->view("templates/header.php");
        $this->load->view("stockmovement/listallnew", $this->view_data);
        $this->load->view("templates/footer.php");
    }

    /**
     * Function to return the data baed on the parameters given from the form.
     *
     * @todo later stage we need to move it one to a service class
     */
    public function downloadstock()
    {
        $this->load->model('Stockmovementlogmodel');

        list($query, $count) = $this->Stockmovementlogmodel->getData($this->view_data);

        $lastname = 'StockMovementLog_'.time().'.csv';
        $filename = APPPATH . '../downloads/'.$lastname;;

        $fp = fopen($filename, 'w');

        fwrite($fp, '"DateTime","From","ProductID","ProductName","Amount","SOH","MovementType","Description","MovementBy","DateCreated"'."\n");

        foreach ($query->result() as $result) 
        {
            if( $result->location_type =="kiosk")
                $FROM =$result->KFrom;
            else
                $FROM =$result->From;   

            $row = array(
                $result->DateTime,
                $FROM,
                $result->SKU,
                $result->ProductName,
                $result->Amount,
                $result->SOH,
                $result->MovementType == "" ? "Dex Read" : $result->MovementType,
                $result->Description,
                $result->MovementBy,
                $result->DateCreated
            );

            fputcsv($fp, $row, ',', '"');
        }

        fclose( $fp);

        $response = array('status' => true, 'result' => true, 'download'=> base_url().'/downloads/'.$lastname, 'message' => "Successful");

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

}
