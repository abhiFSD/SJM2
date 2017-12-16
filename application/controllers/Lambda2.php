<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lambda2 extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // $this->acl->hasAccess();
    }

    public function deploy()
    {
        $data = array();
        $deploy = shell_exec("cd /var/www/html/stockadjustmentproj");
        $data['out'][] = "In document dir $deploy";
        $deploy = shell_exec("git pull");
        $data['out'][] = ($deploy);
        $this->load->view('templates/print', $data);
    }

    private function getType($input, $kid)
    {
        $split = explode($kid, $input);
        $d = substr($split[1], 1);
        $d = substr($d, 0, -4);
        return $d;
    }

    private function parseStar($input)
    {
        return explode("*", $input);
    }

    private function lineValidation($input, $delim)
    {
        return substr($input, 0, 3) === $delim;
    }

    private function getKiostId($input)
    {
        preg_match_all('/(MPP)\d*/', $input, $matches, PREG_PATTERN_ORDER);
        return ($matches[0][0]);
    }

    public function receiveSQS()
    {
        $out = array();
        $this->load->library('Aws');
        $this->load->Model('Dex_Model');
        $client = $this->aws->getSQS('ap-southeast-2');
        $url = "https://sqs.ap-southeast-2.amazonaws.com/495124794474/DEXQueue";
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
            $this->load->model('Picks_Model');
            $this->Picks_Model->Log("New dex event fired at " . date("d-m-Y H:i:s"));
            if ($res->getPath('Messages'))
            {
                foreach($res->getPath('Messages') as $msg)
                {
                    $body = get_object_vars(json_decode($msg['Body']));
                    $this->Picks_Model->Log("TransactionModelInsert - " . date("d-m-Y H:i:s"));
                    $mdate = $body['MachineDate'];
                    $mdate = explode("_", $mdate);
                    $date = $mdate[0];
                    $time = $mdate[1];
                    $dex_read_id = $this->insertDexData($msg);
                    $this->Picks_Model->Log("insertDexData - " . $dex_read_id . " -- " . date("d-m-Y H:i:s"));
                    $datetime = substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2) . ' ' . substr($date, 9, 2) . ':' . substr($date, 11, 2) . ':' . substr($date, 13, 2);
                    $kioskNumber = $body['OperatorIdentifier'];
                    $kioskId = $this->Dex_Model->getKioskId($kioskNumber);
                    $kioskId = $kioskId['id'];
                    $type = $body['Origin'];
                    $data = array();
                    $handle = $body['DexData'];
                    $lines = explode("\n", $handle);
                    $p1 = array();
                    foreach($lines as $key => $buffer)
                    {
                        $PA1 = self::lineValidation($buffer, 'PA1');
                        $PA2 = self::lineValidation($buffer, 'PA2');
                        $line = self::parseStar($buffer);
                        if ($PA1)
                        {
                            if ($line[1] > 0)
                            {
                                $getCurrentPrice = $this->Dex_Model->UpdateOfferingPrice($kioskId, $line[1], $line[2] / 100);
                            }
                            $p1 = array(
                                'kiosk_id' => $kioskId,
                                'position' => $line[1],
                                'price' => $line[2],
                                'datetime' => $datetime
                            );
                        }
                        else if ($PA2)
                        {
                            $p1['total_sales'] = $line[1];
                            $p1['salesvalue'] = $line[2];
                            $data[] = $p1;
                            $p1 = null;
                        }
                    }
                    $out = $this->Dex_Model->saveDexMovement($data, $type, $dex_read_id);
                    $this->Picks_Model->Log("saveDexMovement - " . date("d-m-Y H:i:s"));
                    if ($dex_read_id)
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


    /**
     * Function to store the data from dex into the database
     *
     */
    public function receiveDexQueue()
    {
        $this->load->library('Aws');
        $client = $this->aws->getSQS('ap-southeast-2');
        $url = "https://sqs.ap-southeast-2.amazonaws.com/495124794474/DEXQueue";
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
            if ($res->getPath('Messages'))
            {
                foreach($res->getPath('Messages') as $msg)
                {
                    $result = $this->insertDexData($msg);
                    if ($result)
                    {
                        // Do something useful with $msg['Body'] here
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
    /**
     * Insert data to the dex_data
     *
     * @param $str
     * @return bool
     */
    private function insertDexData($str)
    {
        $this->load->library('Amazonservice');
        $fulldata = get_object_vars(json_decode($str['Body']));
        $data = explode("\r\n", $fulldata['DexData']);
        $insertData = array();
        $insertData['log'] = serialize($str);
        $insertData['dex_trigger'] = $fulldata['Origin'];
        $insertData['machine_number_file'] = $fulldata['OperatorIdentifier'];
        $insertData['message_id'] = $str['MessageId'];
        if ($this->amazonservice->isAlreadyExists($str['MessageId']))
        {
            if ($fulldata['Origin'] == 'GTRACE_LOG')
            {
                return true;
            }
            foreach($data as $line)
            {
                $lineData = explode("*", $line);
                switch (trim($lineData[0]))
                {
                case "DA1":
                    $insertData['device_serial_number'] = substr($lineData[1], strlen($lineData[1]) - 6);
                    // do the last 6 characters
                    break;

                case "CA15":
                    $insertData['coin_balance'] = substr($lineData[1], 0, strlen($lineData[1]) - 2);
                    // remove 2 right most characters
                    break;

                case "CA3":
                    /*$currentNotes = substr($lineData[10], 0, strlen($lineData[10]) - 2);*/
                    $currentNotes = substr($lineData[8], 0, strlen($lineData[8]));
                    $insertData['notes'] = $currentNotes;
                    // remove the right most 2 chars
                    break;

                case "EA3":
                    /*   $datestr = $lineData[2];
                    $year = "20".substr($lineData[2], 0, 2);
                    $month = substr($lineData[2], 2, 2);
                    $day = substr($lineData[2], 4, 2); */
                    $insertData['date_of_read'] = date('Y-m-d', ($str['Attributes']['SentTimestamp']) / 1000);
                    if (strlen($lineData[3]) <= 4)
                    {
                        $timestr = $lineData[3] . "00";
                    }
                    else
                    {
                        $timestr = $lineData[3];
                    }
                    $hour = substr($timestr, 0, 2);
                    $min = substr($timestr, 2, 2);
                    $sec = substr($timestr, 4, 2);
                    $insertData['time_of_read'] = date('H:i:s', ($str['Attributes']['SentTimestamp']) / 1000);
                    break;

                case "MA5":
                    $insertData['temperature'] = $lineData[3];
                    break;

                case "CA6":
                    $insertData['door_openings'] = $lineData[2];
                    break;
                }
                $requireCashCollectionEntry = false;
                // if the DEX TRIGGER doesn't not contain CASH keyword,
                // calculate the notes in machine based on the previous entry of the same machine
                $notes = $this->amazonservice->getNotesInMachine($insertData['machine_number_file'], $currentNotes);
                if (strstr($insertData['dex_trigger'], 'CASH'))
                {
                    $insertData['notes_in_machine'] = 0;
                    $requireCashCollectionEntry = true;
                }
                else
                {
                    $insertData['notes_in_machine'] = $notes;
                }
            }
            try
            {
                // $valueBeforeReset = $this->amazonservice->getLastNoteCount($insertData['machine_number_file']);
                if ($requireCashCollectionEntry)
                {
                    $this->insertCashCollection($insertData, $notes);
                }
                $mid = $insertData['message_id'];
                $query = $this->db->query("select id  from dex_data where message_id = '$mid'");
                if ($query->num_rows() > 0)
                {
                    $data = $query->result_array();
                    return $data[0]['id'];
                }
                else
                {
                    $this->db->insert('dex_data', $insertData);
                    return $this->db->insert_id();
                }
            }
            catch(Exception $e)
            {
                return false;
            }
        }
    }
    private function insertCashCollection($data, $valueBeforeReset)
    {
        $insertData = array(
            'collection_date' => $data['date_of_read'],
            'collection_time' => $data['time_of_read'],
            'machine_number' => $data['machine_number_file'],
            'dex_trigger' => $data['dex_trigger'],
            'collection_amount' => $valueBeforeReset,
            'coin_balance' => $data['coin_balance'],
            'message_id' => $data['message_id']
        );
        try
        {
            $this->db->insert('cash_collection', $insertData);
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    public function test()
    {
        $data['Body'] = '{"HWSerial":"197937","MachineDate":"20160716 135126_217","OperatorIdentifier":"MPP036","Origin":"AUTOMATIC","DexData":"DXS*IDS0000000*VA*V0/6*1\r\nST*001*0001\r\nID1*IDS0110000146*IDS-VCM*0***0\r\nID4*2*036*AUD\r\nCB1*IDS0110000146*IDS-VCM*FIRMWARE 1.17\r\nCA1*NRI10122662-007*C2Pv0 1  455*0400*\r\nBA1*PTIPTI143800056*APEX7000    *0110*\r\nDA1*NYX000000197937*DMX - 2011  *0100*\r\nVA1*8627200*3538*3500*2\r\nVA3*0*0*0*0\r\nCA3*0*0*0*0*3469065*7765*97800*33635*0*3363500\r\nCA4*0*0*701400*0\r\nCA8*0*31565\r\nCA15*37200\r\nCA17*5*100*44*0*0*\r\nCA17*6*200*164*0*0*\r\nTA2*0*0*0*0\r\nPA1*10*1000\r\nPA2*128*128000*0*0\r\nPA1*11*1000\r\nPA2*125*125000*0*0\r\nPA1*12*2000\r\nPA2*110*220000*0*0\r\nPA1*13*2000\r\nPA2*94*188000*0*0\r\nPA1*14*500\r\nPA2*48*24000*1*500\r\nPA1*20*1500\r\nPA2*205*307500*0*0\r\nPA1*21*1500\r\nPA2*221*331500*0*0\r\nPA1*22*1500\r\nPA2*155*232500*0*0\r\nPA1*23*1500\r\nPA2*190*285000*0*0\r\nPA1*24*1500\r\nPA2*247*370500*0*0\r\nPA1*25*1500\r\nPA2*226*339000*0*0\r\nPA1*30*500\r\nPA2*74*37000*0*0\r\nPA1*31*3000\r\nPA2*60*180000*0*0\r\nPA1*32*3000\r\nPA2*110*330000*0*0\r\nPA1*33*3000\r\nPA2*110*330000*0*0\r\nPA1*34*3000\r\nPA2*128*384000*0*0\r\nPA1*35*3000\r\nPA2*91*273000*1*3000\r\nPA1*36*2500\r\nPA2*0*0*0*0\r\nPA1*40*1000\r\nPA2*58*58000*0*0\r\nPA1*41*3500\r\nPA2*55*192500*0*0\r\nPA1*42*3500\r\nPA2*121*423500*0*0\r\nPA1*43*3500\r\nPA2*130*455000*0*0\r\nPA1*44*3500\r\nPA2*143*500500*0*0\r\nPA1*45*3500\r\nPA2*102*357000*0*0\r\nPA1*46*3500\r\nPA2*0*0*0*0\r\nPA1*50*4500\r\nPA2*35*157500*0*0\r\nPA1*51*5000\r\nPA2*34*170000*0*0\r\nPA1*52*5000\r\nPA2*52*260000*0*0\r\nPA1*53*5000\r\nPA2*50*250000*0*0\r\nPA1*54*5000\r\nPA2*51*255000*0*0\r\nPA1*55*5000\r\nPA2*58*290000*0*0\r\nPA1*60*6500\r\nPA2*37*240500*0*0\r\nPA1*61*6500\r\nPA2*52*338000*0*0\r\nPA1*62*6000\r\nPA2*54*324000*0*0\r\nPA1*63*6000\r\nPA2*51*306000*0*0\r\nPA1*70*1500\r\nPA2*32*48000*0*0\r\nPA1*71*1500\r\nPA2*32*48000*0*0\r\nPA1*72*4000\r\nPA2*45*180000*0*0\r\nPA1*73*1000\r\nPA2*24*24000*0*0\r\nPA4*0*0*0*0\r\nDA2*5891100*2212*3500*2\r\nDA4*0*0\r\nEA2*EC_0*0**0*0\r\nEA2*EC_0*0**0*0\r\nEA2*EC_0*0**0*0\r\nEA2*EC_0*0**0*0\r\nEA2*EJH*0***1\r\nEA2*EOA*0**19*0\r\nEA2*EO_1*0**FROST*0\r\nEA2*EO_2*0**OVERRUN*1\r\nEA3**160716*144701**160716*124030***26407*26407\r\nG85*13AB\r\nSE*108*0001\r\nDXE*1*1\r\n"}';
        $this->insertDexData($data);
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
                // update
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
