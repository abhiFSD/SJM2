<?php
/*
* @author: Avtar Gaur
* Description: The Controller for Batch Offering change
*
* Date: Jan 21, 2017
*/
class Batchofferingchange extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->acl->hasAccess();

        $this->load->model('Offeringattributemodel');
        $this->load->model('Offeringattributeallocationmodel');
    }

    /*
    * Function: all()
    * Description: This will show the filtered Batch Offerin change Data
    *
    * @author: Avtar Gaur (developer.avtargaur@gmail.com)
    * Date: Jan 22, 2017
    */
    public function all()
    {
        $planagram_filters = $this->session->userdata('batchofferingchange/manage');

        if (!is_array($planagram_filters))
        {
            $this->session->set_userdata($data['planagram_filters'] = []);
        }

        $options = new stdClass();
        $options->item_category_type = ['Product'];
        $options->order = 'name';

        $this->load->model('Sitemodel');
        $this->load->model('Kioskmodel');
        $this->load->model('ProductModel');
        $this->load->model('Productcategorymodel');

        $capacities = $this->Offeringattributeallocationmodel->get_values_for_variable('Capacity');
        sort($capacities);

        $var_data = [
            'planagram_filters' => $planagram_filters,
            'capacities' => $capacities,
            'kiosks' => POW\Kiosk::get_installed(null),
            'kiosk_models' => $this->Kioskmodel->getkioskModels(),
            'states' => POW\Site::get_states(),
            'positions' => $this->Offeringattributeallocationmodel->get_all_positions(),
            'pars' => $this->Offeringattributeallocationmodel->get_values_for_variable('Par'),
            'site_categories' => $this->Sitemodel->get_site_categories(),
            'product_categories' => $this->Productcategorymodel->get_product_categories(),
            'products' => $this->ProductModel->getAll_products_for_batch()
        ];

        $filter_view = $this->load->view('batchofferingchange/filters', $var_data, true);

        $this->view_data['filters'] = $filter_view;
        $this->view_data['all_products'] = POW\Sku::get_all($options);
        $this->view_data['kiosk_active'] = $this->Kioskmodel->getActivelyDeployedKiosks();
        $this->view_data['widths'] = $this->Offeringattributeallocationmodel->get_values_for_variable('Width');
        $this->view_data['pusher'] = $this->Offeringattributeallocationmodel->get_products_category(29);
        $this->view_data['stabiliser'] = $this->Offeringattributeallocationmodel->get_products_category(30);
        $this->view_data['platform'] = $this->Offeringattributeallocationmodel->get_products_category(31);
        $this->view_data['body_id'] = 'planagram';

        $this->default_view('batchofferingchange/all');
    }

    /**
     * Fucntion to manage the saving part
     *
     */
    public function manage()
    {
        if ($post = $this->input->post())
        {
            $this->session->set_userdata('batchofferingchange/manage', $post);
            $data = POW\Planagram::get($post);
            if (count($data) > 7000)
            {
                echo "<div class='alert alert-danger'>Result set too large. Please refine filter to return less than 200 offerings.</div>
                    <span class=\"pull-right\" id='switchfilter'><a href=\"javascript: void(0)\" onclick=\"$('#filter_section').show();switchFilter();\" class=\"btn btn-primary\">Show Filter</a></span>";
                exit;
            }

            $data['status_queued'] = 0;
            if (isset($post['status'][0]))
            {
                if ($post['status'][0] == "queued" && count($post['status']) == 1)
                {
                    $data['status_queued'] = 1;
                }
            }

            $result = $this->load->view('batchofferingchange/list', array(
                'data' => $data
            ) , true);
            echo trim(str_replace("&nbsp;", "", $result));
        }
    }

    public function save()
    {
        if ($this->input->post('action'))
        {
            if ($this->input->post('position') != "" && $this->input->post('position') != 'false')
            {
                $this->Offeringattributeallocationmodel->saveNewPositionAndData($this->input->post());
                echo 1;
            }
            else
            {
                try
                {
                    switch ($this->input->post('action'))
                    {
                    case "queue":
                        $this->Offeringattributeallocationmodel->queue($this->input->post());
                        break;

                    case "commit":
                        $this->Offeringattributeallocationmodel->commit($this->input->post());
                        break;

                    case "unqueue":
                        $this->Offeringattributeallocationmodel->unqueue($this->input->post());
                        break;
                    }

                    echo 1;
                }

                catch(Exception $e)
                {
                    echo 0;
                }
            }
        }
    }

    public function history()
    {
        $this->load->model('Sitemodel');
        $this->load->model('Kioskmodel');
        $this->load->model('ProductModel');

        $this->view_data['site_categories'] = $this->Sitemodel->get_site_categories();
        $this->view_data['kiosk_models'] = $this->Kioskmodel->getkioskModels();
        $this->view_data['kiosks'] = POW\Kiosk::get_installed(null);
        $this->view_data['states'] = POW\Site::get_states();
        $this->view_data['products'] = $this->ProductModel->getAll_products_for_batch();
        $this->view_data['positions'] = $this->Offeringattributeallocationmodel->get_all_positions();
        $this->view_data['capacities'] = $this->Offeringattributeallocationmodel->get_values_for_variable('Capacity');
        $this->view_data['widths'] = $this->Offeringattributeallocationmodel->get_values_for_variable('Width');
        $this->view_data['pars'] = $this->Offeringattributeallocationmodel->get_values_for_variable('Par');
        $this->view_data['body_id'] = 'planagram-history';

        $this->default_view('batchofferingchange/history');
    }

    public function filter_history()
    {
        if ($post = $this->input->post())
        {
            if (empty($post['kiosk_name']))
            {
                print 'Kiosk name is required.';
            }
            else
            {
                $data = POW\PlanagramHistory::get_history($post);

                $this->load->view('batchofferingchange/history_list', array(
                    'data' => $data
                ));
            }
        }
    }

    public function batchmodifyqueue()
    {
        $this->load->model('Productmodel');

        $post = $this->input->post();
        $out = $this->Offeringattributeallocationmodel->batchmodifyqueue($post);

        if ($out)
        {
            $response = array(
                'status' => true,
                'result' => true,
                'info' => $post,
                'message' => "Successful",
                'redirect' => '/batchofferingchange/history'
            );
        }
        else
        {
            $response = array(
                'status' => false,
                'result' => false,
                'info' => $post,
                'message' => "Error"
            );
        }

        return $this->json_output($response);;
    }

    public function addpositionqueue()
    {
        $post = $this->input->post();
        $output = $this->Offeringattributeallocationmodel->addpositionqueue($post);
        if ($output)
        {
            $response = array(
                'status' => true,
                'result' => true,
                'info' => $post,
                'message' => "Successful",
                'redirect' => '/batchofferingchange/history'
            );
            return $this->json_output($response);;
        }
        else
        {
            $response = array(
                'status' => false,
                'result' => false,
                'info' => $post,
                'message' => "Error"
            );
            return $this->json_output($response);;
        }
    }

    public function deleteOfferingQueue()
    {
        $post = $this->input->post();
        $selected_ids = json_decode($post['json']);
        $isOk = true;
        foreach($selected_ids as $key => $item)
        {
            $out = $this->Offeringattributeallocationmodel->doDeleteOfferingQueue($item);
            if ($out == 0)
            {
                $isOk = false;
            }
        }

        if ($isOk)
        {
            $response = array(
                'status' => true,
                'result' => true,
                'info' => $out,
                'message' => "Successful"
            );
            return $this->json_output($response);;
        }
        else
        {
            $response = array(
                'status' => false,
                'result' => false,
                'info' => $out,
                'message' => "Error"
            );
            return $this->json_output($response);;
        }
    }

    public function ProcessOfferingAttributes()
    {
        if ($post = $this->input->post())
        {
            POW\Classes\CommitTransfer::run($post);

            $user_id = $this->session->userdata('user_id');
            $datetime = date('Y-m-d H:i:s');
            $url = site_url('stockmovement/listall').'/?datetime_3m='.urlencode($datetime).'&user_id='.$user_id;

            $msg = new POW\Classes\Message();
            $msg->redirect($url);
            $msg->send();
        }
    }

    public function ProcessOfferingAttributesSaveDraft()
    {
        if ($post = $this->input->post())
        {
            foreach ($post['offering'] as $offering)
            {
                if (!empty($offering['transfer_item_id']))
                {
                    POW\TransferItem::set_in_kiosk($offering['transfer_item_id']);
                }
            }

            $transfer = POW\Transfer::with_id($post['transfer_id']);
            $transfer->status = 'Partially allotted';
            $transfer->save();

            $transfer_status = new POW\TransferStatus();
            $transfer_status->date_created = date('Y-m-d H:i:s');
            $transfer_status->transfer_id = $post['transfer_id'];
            $transfer_status->status = 'Partially Allotted';
            $transfer_status->location_type = $transfer->location_to_type;
            $transfer_status->location_id = $transfer->location_to_id;
            $transfer_status->user_id = $this->session->userdata('user_id');
            $transfer_status->save();

            $msg = new POW\Classes\Message();
            $msg->redirect(site_url('stockmovement/jobs'));
            $msg->send();
        }
    }

    public function commitbatchqueue()
    {
        $post = $this->input->post();
        $selected_ids = json_decode($post['json']);

        foreach($selected_ids as $key => $item)
        {
            $out = POW\OfferingAttributeAllocation::commit_changes_in_position($this->session->userdata('user_id'), $item[0], $item[1]);
        }

        if ($out)
        {
            $response = array(
                'status' => true,
                'result' => true,
                'info' => $out,
                'message' => "Successful"
            );
        }
        else
        {
            $response = array(
                'status' => false,
                'result' => false,
                'info' => $out,
                'message' => "Error"
            );
        }

        return $this->json_output($response);;
    }

    public function batchunqueue()
    {
        $post = $this->input->post();
        $selected_ids = json_decode($post['json']);
        $isOk = true;

        foreach($selected_ids as $key => $item)
        {
            $out = $this->Offeringattributeallocationmodel->doBatchUnQueue($item);
            if ($out == 0)
            {
                $isOk = false;
            }
        }

        if ($out)
        {
            $response = array(
                'status' => true,
                'result' => true,
                'info' => $out,
                'message' => "Successful"
            );
            return $this->json_output($response);;
        }
        else
        {
            $response = array(
                'status' => false,
                'result' => false,
                'info' => $out,
                'message' => "Error"
            );
            return $this->json_output($response);;
        }
    }

    public function getkioskbyposition()
    {
        $position = $this->input->post('position');
        $out = $this->Offeringattributeallocationmodel->getKioskByPosition($position);

        if (count($out) > 0)
        {
            $response = array(
                'status' => true,
                'result' => true,
                'info' => $out,
                'message' => "Successful"
            );
            return $this->json_output($response);;
        }
        else
        {
            $response = array(
                'status' => false,
                'result' => false,
                'info' => null,
                'message' => "Error"
            );
            return $this->json_output($response);;
        }
    }

    public function commitfromqueue()
    {
        $kiosk_id = $this->input->post('kiosk_id');
        $position = $this->input->post('position');

        $out = POW\OfferingAttributeAllocation::delete_position_attributes($kiosk_id, $position);

        if ($out)
        {
            $response = array(
                'status' => true,
                'result' => true,
                'info' => $out,
                'message' => "Successful"
            );
            return $this->json_output($response);;
        }
        else
        {
            $response = array(
                'status' => false,
                'result' => false,
                'info' => $out,
                'message' => "Error"
            );
            return $this->json_output($response);;
        }
    }

}
