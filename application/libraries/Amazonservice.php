<?php

/**
 * Class Amazon
 *
 * Service class for the dex data
 *
 * @author Prasanth Pillai <prasanthbpillai@gmail.com>
 */

class Amazonservice
{
	public $ci;
	
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->database();
	}

	/**
	 *
	 * Get the new notes value derived from the previous notes entry for the machine
	 *
	 * @param $machine_number
	 * @param $current_value
	 *
	 * @return integer
	 */
	public function getNotesInMachine($machine_number, $current_value)
	{


		list($lastNoteValue, $lastNotesInMachine) = $this->getLastNoteCountBeforeReset($machine_number);

		$newNotesInMachine = ($current_value-$lastNoteValue) + $lastNotesInMachine;

		return $newNotesInMachine;

	}


	/**
	 * Get the last note count from the same machines
	 *
	 * @param $machine_number
	 * @return int
	 */
	public function getLastNoteCountBeforeReset($machine_number)
	{


		$this->ci->db->select('notes, notes_in_machine');
		$this->ci->db->from('dex_data');
		$this->ci->db->where("machine_number_file = '". $machine_number ."'");
		$this->ci->db->order_by('date_of_read desc, time_of_read desc');
		$this->ci->db->limit(1);

		$dexData = $this->ci->db->get();
		$dex = $dexData->result();

		$lastNotesValue = $lastNotesInMachine = 0;

		if ($dex != "") {
			$lastNotesValue = $dex[0]->notes;
			$lastNotesInMachine = $dex[0]->notes_in_machine;
		}

		return array($lastNotesValue, $lastNotesInMachine);


	}

	public function isAlreadyExists($messageId)
    {
        $this->ci->db->select('id');
        $this->ci->db->from('dex_data');
     
     	///$this->ci->db->where("machine_id = '". $messageId ."'");

        $this->ci->db->where("device_serial_number = '". $messageId ."'");
     
        $dexData = $this->ci->db->get();

        if (count($dexData) > 0) {
            return true;
        } else {
            return false;
        }


    }


	
}