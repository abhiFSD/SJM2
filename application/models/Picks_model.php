<?php

class Picks_Model extends CI_Model
{

  const TABLE = 'dex_stock_movement_log';
  const OAA_TABLE = 'offering_attribute_allocation';

  /**
   *
   * Get the dex movement by position
   *
   * @param integer $id
   * @return string
   */
  public function getByPosition($id,$kid, $datetime)
  {
    $query = $this->db->query("SELECT total_sales, date_created  FROM `dex_stock_movement_log` where position=$id and kiosk_id='$kid' order by id desc limit 1");

    if ($query->num_rows() > 0) {
      $data = $query->result_array();

      return $data[0];
    } else {

      return false;
    }
  }


    /**
     * Function to get all  positions by kiosk
     *
     * @return array
     *
     */
    public function getAllPositionsByKiosk($kioskID)
    {
      $this->db->select('position');
      $this->db->from('offering_attribute_allocation');

      $conds = array('kiosk_id' => $kioskID);
      $this->db->where($conds);
      $this->db->order_by("position", "DESC");
      $this->db->distinct();

      $query = $this->db->get();

      if ($query->num_rows() > 0) {

        return ($query->result());
      } else {
        return 0;
      }
    }

    /**
     * Function to get all  positions by kiosk
     *
     * @return array
     *
     */
    public function getAllQueuedItem($kioskID,$position)
    {
      $query = $this->db->query("SELECT *  FROM `offering_attribute_allocation` WHERE   `status` = 'Queued' and kiosk_id=$kioskID and position =$position");

      if ($query->num_rows() > 0) {
        $data = $query->result_array();

        return  $data;
      } else {

        return 0;
      }
    }

    public function getAllItem($kioskID,$position){

      $query = $this->db->query("SELECT *  FROM `offering_attribute_allocation` WHERE  kiosk_id=$kioskID and position =$position");

      if ($query->num_rows() > 0) {
        $data = $query->result_array();
        $out = array();
        foreach( $data as $k=>$d)
        {

         $out[$d['offering_attribute_id']]['queued'] = 0;

         if($d['status'] == 'Active' && $out[$d['offering_attribute_id']]['queued'] == 0){
           $out[$d['offering_attribute_id']] = $d;

         }

         if($d['status'] == 'Queued'){
          $out[$d['offering_attribute_id']]['queued'] = 1;
          $out[$d['offering_attribute_id']] = $d;
        }

      }
      return $out;
    } else {

      return false;
    }

  }


    public function getAllActiveItem($kioskID,$position)
    {
        $query = $this->db->query("SELECT *  FROM `offering_attribute_allocation` WHERE  `status` = 'Active' and kiosk_id=$kioskID and position =$position");

        if ($query->num_rows() > 0) 
        {
            $data = $query->result_array();

            return $data;
        } else {
            return false;
        }
    }

    public function Log($message)
    {
        $ts['log'] =  $message;
        $this->db->insert('log', $ts);
    }

    public function qbuilderlikeAllSearch($data, $id)
    {
        $q  = '(';

        foreach ( $data as $key => $name) {
            $q  .= " concat(k.number,' ',kl.name) like '%" . $name . "%' or ";
        }

        $q =  substr(trim($q), 0, -2);
        $q  .= ')';

        return $q;
   }

    public function qbuilder($data, $id)
    {
        $q  = '(';

        foreach ( $data as $key => $name) {
            $q  .= " $id = '" . $name . "' or ";
        }

        $q =  substr(trim($q), 0, -2);
        $q  .= ')';

        return $q;
    }


    /**
     *
     * Get all kiosks
     *
     * @param integer $id
     * @return string
     */
    public function getAllKiosks($criteria, $order)
    {
        if ($criteria!="")
        {
            $arr = explode("|", $criteria['kiosk']);

            if(isset( $arr[1]))
                $criteria['status'] = $arr[1];
            else
                $criteria['status'] ="";

            $criteria['kiosk'] = $arr[0];
        }

        $sqlCriteria = array();
        if (isset($criteria['kiosk']) && $criteria['kiosk'] != ""  && $criteria['kiosk'] != 'null' ) 
        {
            $ks = (explode(",", $criteria['kiosk']));
            $sqlCriteria[] = self::qbuilder( $ks, "k.number");
        }

        if (isset($criteria['state']) && $criteria['state'] != ""  && $criteria['state'] != 'null' ) 
        {
            $ks = (explode(",", $criteria['state']));
            $sqlCriteria[] = self::qbuilder( $ks, "s.state");
        }

        if (isset($criteria['search']) && $criteria['search'] != "" && $criteria['search'] != 'null' ) 
        {
            $ks = (explode(",", $criteria['search']));
        }

        if (isset($criteria['search']) && $criteria['search'] != "" && $criteria['search'] != 'null' ) 
        {
            $ks = (explode(",", $criteria['search']));
            $sqlCriteria[] = self::qbuilderlikeAllSearch( $ks, "k.number");
        }

        $sql = "
            SELECT 
                SQL_CALC_FOUND_ROWS 
                DISTINCT 
                k.number, 
                k.id as kid, 
                kl.name as name, 
                s.state,transfer.id as transfer_id, 
                transfer.location_to_id, 
                transfer.date_created,
                transfer.location_from_id, 
                transfer.location_to_id  
            from kiosk as k
            left JOIN kiosk_deployment AS kd ON kd.machine_id = k.id
            left JOIN kiosk_location AS kl ON kl.id = kd.location_id
            left JOIN site AS s ON kl.site_id = s.id
            left join transfer on k.id = transfer.location_to_id
            left JOIN transfer_status AS ts ON ts.transfer_id = transfer.id
            left JOIN inventory_location ON inventory_location.id = transfer.location_from_id
        ";

        if (count($sqlCriteria) > 0) 
        {
            $sql .= " where " . implode(" and ", $sqlCriteria) . "  and ( k.status = 'Active' and  ( kd.status = 'Installed' or  kd.status = 'Removal Scheduled' ) )";
        }
        else
        {
            $sql .= " where  ( k.status = 'Active'  and  ( kd.status = 'Installed' or  kd.status = 'Removal Scheduled' )  ) ";
        }

        $sql .=  " group by k.number";

        if (!empty($order['field']))
        {
            $sql .=  " order by ".$order['field']." ".$order['direction']." ";
        }

        $query = $this->db->query($sql);
        $rows = $query->result_array();
        $query = $this->db->query('SELECT FOUND_ROWS() AS `Count`');

        $data["totalres"] = $query->row()->Count;
        $data['found'] = $data["totalres"];
        $data['rows'] = $rows;

        if ($query->num_rows() > 0) 
        {
            return $data;
        } 
        else 
        {
            return false;
        }
    }

  /**
   *
   * Get the change price
   *
   * @param integer $id
   * @return string
   */
    public function getPriceChanges($kid,$positions)
    {
        $out = array();

        if( $positions !=0  )
        {
            foreach ($positions as $keys => $position) 
            {
                $query = $this->db->query("SELECT * FROM `offering_attribute_allocation` left join kiosk on kiosk.id=offering_attribute_allocation.kiosk_id where kiosk.id='$kid' and offering_attribute_allocation.offering_attribute_id = 2 and position=".$position->position." and offering_attribute_allocation.status='Active' ");

                $query1 = $this->db->query("SELECT * FROM `offering_attribute_allocation` left join kiosk on kiosk.id=offering_attribute_allocation.kiosk_id where kiosk.id='$kid' and offering_attribute_allocation.offering_attribute_id = 3 and position=".$position->position." and offering_attribute_allocation.status='Active' ");

                $data = $query->num_rows() ? $query->result_array() : [];
                $data1 = $query1->num_rows() ? $query1->result_array() : [];

                if(count($data) > 0 && count($data1) )
                {
                    $time  = strtotime($data[0]['date_applied']);
                    $time1  = strtotime($data1[0]['date_applied']);

                    if($data1[0]['value']!=$data[0]['value'] ){
                        $out[$position->position]['price']= $data[0]['value'];
                        $out[$position->position]['position']=$position->position;
                        $out[$position->position]['dexprice']=$data1[0]['value'];
                        $out[$position->position]['last_dex_time']=$data1[0]['date_applied'];
                    }
                }
            }
        }

        return  $out;
    }

}
