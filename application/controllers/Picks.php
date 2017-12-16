<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Picks extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Offeringattributemodel');
        $this->load->model('Offeringattributeallocationmodel');
    }

    public function all()
    {
        $this->load->model('Sitemodel');

        $data['kiosks'] = POW\Kiosk::get_installed(null);
        $data['states'] = POW\Site::get_states();
        $data['sites'] = POW\Site::in_state([]);

        $this->load->view("templates/header.php");
        $this->load->view('picks/all', $data);
        $this->load->view("templates/footer.php");
    }

    public function getKiosks()
    {
        $site_id = $this->input->get('site_id');
        $site_id = $site_id && $site_id != 'null' ? explode(',', $site_id) : [];

        $options = new stdClass();
        $options->site_id = $site_id;

        $kiosks = POW\Kiosk::get_installed($options);

        foreach($kiosks as $kiosk)
        {
            echo "<option value='" . $kiosk->number . "' data-id='" . $kiosk->number . "' selected>" . $kiosk->number . " - " . $kiosk->location_name . "</option>\n";
        }
    }

    public function pickpack($transfer_id)
    {
        if (!$transfer_id)
        {
            show_404();
        }

        $this->load->helper('my_url');
        $this->load->model('Kioskmodel');

        $data['transfer'] = $transfer = POW\Transfer::with_id($transfer_id);
        $data['items'] = POW\TransferItem::with_transfer_id($transfer_id);
        $data['selections'] = $this->Kioskmodel->getKioskByConditionPick($transfer->location_to_id);

        $this->load->view("templates/header.php");
        $this->load->view('picks/pickpack', $data);
        $this->load->view("templates/footer.php");
    }

    public function putaway($transfer_id)
    {
        if (!$transfer_id)
        {
            show_404();
        }

        $this->load->helper('my_url');
        $this->load->model('Picks_Model');
        $this->load->model('Kioskmodel');
        $this->load->helper('planagram');

        $transfer = POW\Transfer::with_id($transfer_id);

        if ($transfer->location_to_type == 'kiosk')
        {
            $positions = $this->Picks_Model->getAllPositionsByKiosk($transfer->location_to_id);
            $queued_positions = POW\OfferingAttributeAllocation::get_queued_non_product_positions($transfer->location_to_id);
            
            $this->view_data['items'] = POW\TransferItem::get_picks($transfer);
            $this->view_data['selections'] = $this->Kioskmodel->getKioskByConditionPick($transfer->location_to_id);
            $this->view_data['kiosk_config_values'] = $this->Kioskmodel->getKioskConfigValues($transfer->location_to_id);
            $this->view_data['price_changes'] = $this->Picks_Model->getPriceChanges($transfer->location_to_id, $positions);
            $this->view_data['planagram_changes'] = count($queued_positions) ? POW\Planagram::get([
                'position' => $queued_positions,
                'kiosk_name' => [$transfer->location_to_id],
                'transfer_id' => $transfer_id
            ]) : [];
        }
        elseif ($transfer->location_to_type == 'inventory_location')
        {
            $this->view_data['transfer_items'] = POW\TransferItem::with_transfer_id($transfer->id);
        }

        $this->view_data['transfer'] = $transfer;
        $this->view_data['body_id'] = 'atKioskList';

        $this->default_view('picks/atkiosk');
    }

    public function applykioskconfig()
    {
        if ($post_data = $this->input->post())
        {
            $this->load->model('Kioskmodel');
            foreach($post_data as $keys => $value)
            {
                list($status, $kiosk_config_item_id, $config_item_id) = explode('|', $keys);

                if (!in_array($status, ['active', 'queued'])) continue;

                if ($value == 'Select')
                {
                    $value = null;
                }

                if ($status == 'active' && !empty($value))
                {
                    $this->Kioskmodel->updateKioskConfiguration($post_data['kiosk_id'], $status, $config_item_id, $value);
                }
                elseif ($status == 'queued')
                {
                    if (
                        (empty($kiosk_config_item_id) && !empty($value)) ||
                        // check if queued value needs to be replaced
                        (!empty($kiosk_config_item_id) && !empty($value))
                    )
                    {
                        $this->Kioskmodel->updateKioskConfiguration($post_data['kiosk_id'], $status, $config_item_id, $value);
                    }
                }
            }

            redirect('picks/putaway/'.$post_data['transfer_id']);
        }
    }

    public function getpricetable()
    {
        $this->load->model('Picks_Model');

        $kiosk_id = $this->input->post('kiosk_id');
        $positions = $this->Picks_Model->getAllPositionsByKiosk($kiosk_id);
        $data['price_changes'] = $this->Picks_Model->getPriceChanges($kiosk_id, $positions);

        if (count($data['price_changes']) > 0)
        {
            foreach($data['price_changes'] as $key => $p)
            { ?>
          <tr>
           <td scope="row"><?php
                echo $p['position'] ?></td>
            <td><?php
                echo $p['last_dex_time'] ?> </td>
            <td> <?php
                echo $p['dexprice'] ?></td>
            <td scope="row"><?php
                echo $p['price'] ?> </td>
        </tr>
        <?php
            }
        }

        if (count($data['price_changes']) == 0) return 0;
    }

    public function deletetransfer()
    {
        if ($this->input->post())
        {
            $transfer_id = $this->input->post("transfer_id");

            $removeTransfer = POW\Transfer::delete_with_id($transfer_id);

            if ($removeTransfer)
            {
                $response = array(
                    'status' => true,
                    'result' => true,
                    'info' => 1,
                    'message' => "Successful"
                );
            }
            else
            {
                $response = array(
                    'status' => false,
                    'result' => false,
                    'info' => 0,
                    'message' => "Error"
                );
            }

            $this->json_output($response);
        }
    }

    public function dopick()
    {
        $post = $this->input->post();

        $transfer = POW\Transfer::with_id($post['transfer_id']);
        $transfer->status = $post['type'];
        $transfer->save();

        $transfer_status = new POW\TransferStatus();
        $transfer_status->date_created = date('Y-m-d H:i:s');
        $transfer_status->status = $post['type'];
        $transfer_status->transfer_id = $transfer->id;
        $transfer_status->user_id = $this->session->userdata('user_id');
        $transfer_status->location_type = $transfer->location_from_type;
        $transfer_status->location_id = $transfer->location_from_id;
        $transfer_status->save();

        if (!empty($post['transfer_item_id']))
        {
            foreach($post['transfer_item_id'] as $key => $value)
            {
                $picked_quantity = $post['transfer_item_quantity'][$key];

                if (!empty($picked_quantity))
                {
                    $transfer_item = POW\TransferItem::with_id($value);
                    $transfer_item->picked_quantity = $picked_quantity;
                    $transfer_item->save();
                }
            }
        }

        $this->json_output([
            'status' => true,
            'result' => true,
            'info' => 1,
            'message' => "Successful"
        ]);
    }

    public function generate($type)
    {
        if ($post = $this->input->post())
        {
            $transfers_updated = [];
            $transfers_not_updated = [];
            $msg = new POW\Classes\Message();
            $msg->exec('remove_backdrop');

            foreach ($post['transfer_ids'] as $transfer_id)
            {
                $transfer = POW\Transfer::with_id($transfer_id);
                $transfer_items_count = 0;

                if ($transfer->location_from_type == 'inventory_location' && $transfer->location_to_type == 'kiosk' && $transfer->last_transfer_status->status == 'Job Created')
                {
                    $transfer_items_count = POW\Classes\PickGenerator::run($transfer, $this->session->userdata('user_id'), $type);
                }

                if ($transfer_items_count)
                {
                    $transfers_updated[] = $transfer_id;
                }
                else
                {
                    $transfers_not_updated[] = $transfer_id;
                }
            }

            if (count($transfers_updated))
            {
                $msg->exec('bootstrap_alert', ['success', 'Generated picks for '.plural(count($transfers_updated), 'job', 'jobs').' '.implode(', ', $transfers_updated)]);
            }
            if (count($transfers_not_updated))
            {
                $msg->exec('bootstrap_alert', ['danger', 'Did not generate picks for '.plural(count($transfers_not_updated), 'job', 'jobs').' '.implode(', ', $transfers_not_updated)]);
            }
            
            if (!count($transfers_updated) && !count($transfers_not_updated))
            {
                $msg->exec('bootstrap_alert', ['warning', 'No picks generated']);
            }
            elseif (count($transfers_updated))
            {
                $msg->exec('submit_filter'); 
            }

            $msg->send();
        }
    }

    public function table()
    {
        $this->load->model('Picks_Model');

        $post = $this->input->get();

        $filter['kiosk'] = $post['columns']['1']['search']['value'];
        if ($filter['kiosk'] != "")
        {
            $arr = explode("|", $filter['kiosk']);
            if (isset($arr[1])) $criteria['status'] = $arr[1];
            else $criteria['status'] = "";
            $criteria['kiosk'] = $arr[0];
        }

        $filter['search'] = $post['search']['value'];
        $order['field'] = $post['order'][0]['column'];
        if ($order['field'] == 1) $order['field'] = "k.number";
        if ($order['field'] == 2) $order['field'] = "ts.status";
        $order['direction'] = $post['order'][0]['dir'];

        $data = array();

        $list = $this->Picks_Model->getAllKiosks($filter, $order);
        foreach($list['rows'] as $row)
        {
            $new_row = array();
            $transfer_statuses = [];
            $status = '';

            if (!empty($row['transfer_id']))
            {
                $transfer_statuses = POW\TransferStatus::open_with_kiosk_id($row['kid']);

                if (count($transfer_statuses))
                {
                    $transfer_status = end($transfer_statuses);
                    $status = $transfer_status->status;
                }
            }

            if (!empty($criteria['status']))
            {
                $status_array = explode(",", $criteria['status']);
                $isMatched = false;

                foreach($status_array as $filter)
                {
                    if ($filter == '-1' && !in_array(strtolower($status), ['fully picked', 'partially picked', 'pick generated', 'warehouse returns']))
                    {
                        $isMatched = true;
                        break;
                    }
                    elseif (strtolower($filter) == strtolower($status) && $filter != "-1")
                    {
                        $isMatched = true;
                        break;
                    }
                }

                if (!$isMatched) continue;
            }

            if (!empty($row['transfer_id']))
            {
                $new_row[] = "<input data-status='" . $status . "' type='checkbox' data-id='" . $row['kid'] . "' data-number='" . $row['number'] . "' class='pick-checkboxes' name='check'/>";
            }
            else
            {
                $new_row[] = "<input type='checkbox'  data-id='" . $row['kid'] . "' data-number='" . $row['number'] . "' class='pick-checkboxes' name='check'/>";
            }

            $new_row[] = "<div class='fixwidth'>" . $row['number'] . ' - ' . $row['name'] . '</div>';
            $new_row[] = $this->load->view('picks/sections/pick_list_list', ['row' => $row, 'transfer_statuses' => $transfer_statuses], true);

            $data[] = $new_row;
        }

        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $list['found'],
            "recordsFiltered" => $list['found'],
            "data" => $data,
        );

        $this->json_output($output);
    }
}
