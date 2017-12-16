<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Amazon extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // $this->acl->hasAccess();
    }

    public function receiveSQS()
    {
        $this->load->library('Aws');
        $client = $this->aws->getSQS('us-west-2');
        $url = "https://sqs.us-west-2.amazonaws.com/495124794474/NayaxQueue";
        $this->load->model('Picks_Model');
        $notempty = true;
        while ($notempty)
        {
            $res = $client->receiveMessage(array(
                'QueueUrl' => $url,
                'WaitTimeSeconds' => 1,
                'AttributeNames' => array(
                    'SentTimestamp'
                ) ,
                'MaxNumberOfMessages' => 1
            ));
            $this->Picks_Model->Log("Transaction process started - " . date("d-m-Y H:i:s"));
            if ($res->getPath('Messages'))
            {
                foreach($res->getPath('Messages') as $msg)
                {
                    $data = get_object_vars(json_decode($msg['Body']));
                    $this->load->model('TransactionModel');
                    $id = $this->TransactionModel->insert($data);
                    $date_read = date('Y-m-d H:i:s', ($msg['Attributes']['SentTimestamp']) / 1000);
                    $this->ProcessTransactionSQS($data, $date_read);
                    $this->Picks_Model->Log("TransactionModel Insert - " . $id . " -" . date("d-m-Y H:i:s"));
                    $this->Picks_Model->Log(serialize($msg));
                    if ($id)
                    {
                        $res = $client->deleteMessage(array(
                            'QueueUrl' => $url,
                            'ReceiptHandle' => $msg['ReceiptHandle']
                        ));
                    }
                }
            }
            else
            {
                $notempty = false;
            }
        }
    }

    public function ProcessTransactionSQS($data, $date_read)
    {
        $this->load->Model('Dex_Model');

        $data = get_object_vars($data['Data']);
        $kiosk_number = $data["Operator Identifier"];
        $position = $data["Product PA Code"];
        if ($position == "" || $position < 1)
        {
            return false;
        }

        $item_id = $data["Catalog Number"];
        $adjustment_date = date("Y-m-d H:i:s", strtotime($data["Authorization Time"]));
        $kiosk = POW\Kiosk::with_number($kiosk_number);
        $sku_id = null;

        if ($oaas = POW\OfferingAttributeAllocation::with_offering_attribute_id($kiosk->id, $position, 1, 'Active'))
        {
            foreach ($oaas as $oaa)
            {
                $sku_id = $oaa->value;
            }
        }

        $previous_on_hand = $this->Dex_Model->getPrevOnHand($kiosk->id, $position);
        $new_SOH = (int)$previous_on_hand['value'] - 1;
        if ($new_SOH < 0)
        {
            $new_SOH_adj = 0;
        }
        else
        {
            $new_SOH_adj = $new_SOH;
        }

        $o = $this->Dex_Model->setOnand($new_SOH, $kiosk->id, $position);

        $data = array(
            'location_type' => "kiosk",
            'location_id' => $kiosk->id,
            'item_id' => $sku_id,
            'adjustment_type' => "Sale",
            'adjustment_amount' => - 1,
            'SOH' => $new_SOH_adj,
            'user_id' => "Txn Feed",
            'adjustment_date' => $date_read,
            'date_created' => date("Y-m-d H:i:s") ,
            'date_updated' => date("Y-m-d H:i:s") ,
            'description' => $position
        );

        $stock_movement_log = new POW\StockMovementLog();
        $stock_movement_log->assign_array($data);
        $stock_movement_log->save();
    }

    public function test()
    {
        $data['Body'] = '{
                  "TransactionId": 3158354988,
                  "PaymentMethodId": 1,
                  "SiteId": 4,
                  "MachineTime": "2017-09-14T15:36:17.49",
                  "Void": false,
                  "Data": {
                    "Machine Name": "RMIT CBD Building 12 Level 4",
                    "Operator Identifier": "MPP011",   
                    "Machine AuTime": "2017-09-14T15:36:17.49",
                    "Machine SeTime": "2017-09-14T15:36:17.49",
                    "Card String": "5217 xxxx xxxx 4902",
                    "Brand": "MASTERCARD",
                    "CLI": null,
                    "SeValue": 10.0000,
                    "Extra Charge": null,
                    "Catalog Number": "POW-PA003",  
                    "Actor Hierarchy": "Nayax Australia / Powerpod / PowerPod Vic EMV / ",
                    "Payment Method Description": "Credit Card",
                    "Recognition Description": "Credit Card",
                    "Card First 4 Digits": "5217",
                    "Card Last 4 Digits": "4902",
                    "Card Type": "Contact Reader",
                    "Transaction ID": 3158354988,
                    "Authorization Time": "2017-09-14T05:36:17.49",  
                    "Authorization Value": 10.0000,
                    "Settlement Time": "2017-09-14T05:36:17.49",
                    "Product Code in Map": 12,  
                    "Authorization RRN": "91384397",
                    "Merchant ID": "5198",
                    "Product PA Code": "12",
                    "OP Button Code": "12",
                    "Default Price": 10.0000,
                    "Card Holder Name": null,
                    "Billing Provider Name": "Card Access Services",
                    "Is Offline Transaction": null,
                    "Is EMV Transaction": true
                }
            }';
        $this->ProcessTransactionSQS(get_object_vars(json_decode($data['Body'])), date('Y-m-d H:i:s'));
    }

    public function build()
    {
        $this->db->distinct();
        $this->db->select('machine_number_file');
        $query = $this->db->get('dex_data');
        foreach($query->result() as $distinctData)
        {
            $data = $this->db->order_by('date_of_read, time_of_read')->get_where('dex_data', "machine_number_file = '{$distinctData->machine_number_file}'");
            $machineNumber = null;
            $prevNotes = $prevNotesInMachine = null;
            foreach($data->result() as $dex)
            {
                if ($machineNumber != $dex->machine_number_file)
                {
                    $prevNotes = $prevNotesInMachine = 0;
                    $machineNumber = $dex->machine_number_file;
                    $currentNotesInMachine = 0;
                }
                else
                {
                    $currentNotesInMachine = ($dex->notes - $prevNotes) + $prevNotesInMachine;
                }

                if (strpos($dex->dex_trigger, 'CASH') !== false)
                {
                    $cashCollectionArray = array(
                        "collection_date" => $dex->date_of_read,
                        "collection_time" => $dex->time_of_read,
                        "machine_number" => $dex->machine_number_file,
                        "dex_trigger" => $dex->dex_trigger,
                        "collection_amount" => $currentNotesInMachine,
                        "coin_balance" => $dex->coin_balance
                    );
                    $this->db->insert('cash_collection', $cashCollectionArray);
                    $currentNotesInMachine = 0;
                }
                $updatedValues = array(
                    "notes_in_machine" => $currentNotesInMachine
                );
                $this->db->update('dex_data', $updatedValues, array(
                    'id' => $dex->id
                ));
                $prevNotes = $dex->notes;
                $prevNotesInMachine = $currentNotesInMachine;
            }
        }
    }
}
