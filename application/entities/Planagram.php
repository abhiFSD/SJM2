<?php

namespace POW;

/****
 This is not a real table. This is a convenience data mapper class
 ****/

class Planagram extends BaseModel
{
    protected static $fillable = [
        'id',
        'name',
        'sales_multiplier',
        'site_id',
        'level',
        'location_within_site',
        'nearest_loading_dock_parking',
        'warehouse_id',
        'lat',
        'lng',
        'status',
    ];

    /**
     * Function to fetch the offerings based on criteria
     * @param array $criteria
     * @return bool
     */
    protected static function findBy($criteria = array())
    {
        $oaa_where = [];

        if (!empty($criteria['commit_type']) && count($criteria['commit_type']) == 1) 
        {
            if ($criteria['commit_type'][0] == 1)
            {
                $oaa_where[] = '(`commit_type` = 1 OR `commit_type` IS NULL)';
            }
            else
            {
                $oaa_where[] = '`commit_type` = 0';
            }
        }

        $oaa_where = !empty($oaa_where) ? 'WHERE '.implode(' AND ', $oaa_where) : '';

        $sql = "SELECT DISTINCT
                oaa.id,
                oaa.kiosk_id,
                oaa.position,
                oaa.attribute_ids,
                oaa.attribute_values,
                oaa.attribute_status,
                oaa.status,
                oaa.queue_type,
                oaa.commit_type,
                k.number,
                kl.name,
                kl.warehouse_id
            FROM
                (
                    SELECT
                        id,
                        kiosk_id,
                        `position`,
                        status,
                        offering_attribute_id,
                        queue_type,
                        `value`,
                        GROUP_CONCAT(offering_attribute_id) AS attribute_ids,
                        GROUP_CONCAT(value) AS attribute_values,
                        GROUP_CONCAT(status) AS attribute_status,
                        GROUP_CONCAT(IFNULL(commit_type, 1)) AS commit_type
                    FROM
                        offering_attribute_allocation
                    {$oaa_where}
                    GROUP BY kiosk_id , position , status
                ) AS oaa
                LEFT JOIN offering_attribute_allocation AS oaa_sku on 
                    oaa_sku.kiosk_id = oaa.kiosk_id and 
                    oaa_sku.position = oaa.position and 
                    oaa_sku.status = 'Active' and
                    oaa_sku.offering_attribute_id = 1 
                JOIN kiosk AS k ON oaa.kiosk_id = k.id
                JOIN kiosk_model AS km ON km.id = k.kiosk_model_id
                JOIN kiosk_deployment AS kd ON kd.machine_id = k.id AND kd.status = 'Installed'
                JOIN kiosk_location AS kl ON kl.id = kd.location_id
                JOIN site AS s ON kl.site_id = s.id";

        $sqlCriteria = array();
        $sqlCriteria[] =  " ( kl.status='Active' )";
        if (!empty($criteria['site_category'])) {
            $sqlCriteria[] = self::where_in($criteria['site_category'],"s.category");
        }
        if (!empty($criteria['kiosk_model'])) {
            $sqlCriteria[] = self::where_in($criteria['kiosk_model'],"km.id");
        }
        if (!empty($criteria['kiosk_name'])) {
            $sqlCriteria[] = self::where_in($criteria['kiosk_name'],"k.id");
        }
        if (!empty($criteria['state'])) {
            $sqlCriteria[] = self::where_in($criteria['state'],"s.state");
        }
        if (!empty($criteria['position'])) {
            $sqlCriteria[] = self::where_in($criteria['position'],"oaa.position");
        }
        if (!empty($criteria['capacity'])) {
            $sqlCriteria[] = self::group_extract($criteria['capacity'], 5); 
        } 
        if (!empty($criteria['par'])) {
            $sqlCriteria[] = self::group_extract($criteria['par'], 6); 
        }
        if (!empty($criteria['status'])) {
            $sqlCriteria[] = self::where_in($criteria['status'],"oaa.status");
        }

        if (!empty($criteria['product']) || !empty($criteria['item_type']) || !empty($criteria['item_category'])) 
        {
            $sql .= " \n   JOIN sku ON sku.id = oaa_sku.value ";
            $sql .= " \n   JOIN item ON item.id = sku.product_id \n";

            if (!empty($criteria['product'])) {
               $sqlCriteria[] = " sku.id IN (".implode(',', $criteria['product']).") \n";
            }
            if (!empty($criteria['item_category'])) {
               $sqlCriteria[] = " item.product_category_id IN ('".implode("','", $criteria['item_category'])."') \n";
            }
        } 
        
        if (count($sqlCriteria) > 0) {
            $sql .= " where " . implode(" and ", $sqlCriteria);
        }
        $sql .= " order by k.number asc, oaa.position asc";
        $sql .= " limit 250";

        $query = self::getdb()->query($sql);

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return [];
        }
    }

    protected static function where_in($data, $id)
    {
        $db = self::getdb();

        return "($id IN (".implode(',', array_map(function($item) use ($db) { return $db->escape($item); }, $data))."))";
    }

    protected static function group_extract($data, $id)
    {
        $q = "(";
        foreach ($data as $name)
        {
            $q .= "SUBSTRING_INDEX( SUBSTRING_INDEX( oaa.attribute_values, ',', find_in_set(".$id." ,oaa.attribute_ids) ), ',', -1 ) = ".$name." OR ";
        }

        return substr(trim($q), 0, -2).")";
    }

    public static function get($criteria = array())
    {
        $data = self::findBy($criteria);

        $allocationArray = [];

        if (count($data)) 
        {
            foreach ($data as $allocation)
            {
                $price = $price3 = $par = $capacity = $sku = $onHand = $width = $coil_sku_id = $pusher_sku_id = $stabiliser_sku_id = $platform_sku_id = $label_value = "";

                $propertyIds = explode(",", $allocation['attribute_ids']);
                $propertyValues = explode(",", $allocation['attribute_values']);
                $propertyStatus = explode(",", $allocation['attribute_status']);

                if (array_search('6', $propertyIds) !== false) {
                    $par = $propertyValues[array_search('6', $propertyIds)];
                }
                $price3 = 0;
                $price = 0;
                if (array_search('3', $propertyIds) !== false) {
                    $price3 = floatval($propertyValues[array_search('3', $propertyIds)]);
                }
                if (array_search('2', $propertyIds) !== false) {
                    $price = floatval($propertyValues[array_search('2', $propertyIds)]);
                }
                if (!empty($criteria['min_price'])) {
                    if( $price < $criteria['min_price'])
                    continue;
                }
                if (!empty($criteria['max_price'])) {
                    if( $price > $criteria['max_price'])
                    continue;
                }

                if (!empty($criteria['price_issue']))
                {
                    if ($price == $price3) continue;

                    if (1 == count($criteria['price_issue']) && in_array('aroma-gt-kiosk', $criteria['price_issue']))
                    {
                        if ($price < $price3) continue;
                    }
                    if (1 == count($criteria['price_issue']) && in_array('kiosk-gt-aroma', $criteria['price_issue']))
                    {
                        if ($price > $price3) continue;
                    }
                }

                if (array_search('4', $propertyIds) !== false) {
                    $onHand = $propertyValues[array_search('4', $propertyIds)];
                }
                if (array_search('5', $propertyIds) !== false) {
                    $capacity = $propertyValues[array_search('5', $propertyIds)];
                }
                if (array_search('1', $propertyIds) !== false) {
                    $sku = $propertyValues[array_search('1', $propertyIds)];
                }
                if (array_search('8', $propertyIds) !== false) {
                    $width = $propertyValues[array_search('8', $propertyIds)];
                }
                if (array_search('9', $propertyIds) !== false) {
                    $coil_sku_id= $propertyValues[array_search('9', $propertyIds)];
                }
                if (array_search('10', $propertyIds) !== false) {
                    $pusher_sku_id = $propertyValues[array_search('10', $propertyIds)];
                }
                if (array_search('11', $propertyIds) !== false) {
                    $stabiliser_sku_id = $propertyValues[array_search('11', $propertyIds)];
                }
                if (array_search('12', $propertyIds) !== false) {
                    $platform_sku_id = $propertyValues[array_search('12', $propertyIds)];
                }
                if (array_search('13', $propertyIds) !== false) {
                    $label_value = '13|'.$propertyValues[array_search('13', $propertyIds)];
                }

                $ci = get_instance();

                $status = $allocation['status'] ? $allocation['status'] : "Active";
                $product = Sku::get_sku_with_item($sku);

                // get the SOH for the product on the inventory
                $ci->load->model('Inventoryitemmodel');

                $coil = Sku::with_id($coil_sku_id);
                $pusher = Sku::with_id($pusher_sku_id);
                $stabiliser = Sku::with_id($stabiliser_sku_id);
                $platform = Sku::with_id($platform_sku_id);

                $SOH = !intval($sku) ? 0 : InventoryItem::adjusted_sku_soh($allocation['warehouse_id'], $sku);

                $data = array(
                    "position" => $allocation['position'],
                    "number" => $allocation['number'],
                    "name" => $allocation['name'],
                    "queue_type" => $allocation['queue_type'],
                    "queue_status" => $propertyIds,
                    "par" => $par,
                    "price" => $price,
                    "price3" => $price3,
                    "on_hand" => $onHand,
                    "capacity" => $capacity,
                    "skuid" => $product->id,
                    "sku-value"=>$product->sku_value,
                    "product" => $product->name,
                    "item" => $product->item_name,
                    "width" => $width,
                    "coil" => $coil->name,
                    "pusher" => $pusher->name,
                    "stabiliser" => $stabiliser->name,
                    "platform" =>$platform->name,
                    "label_value" => $label_value,
                    "id" => $allocation['id'],
                    "SOH" => $SOH,
                );

                if (!empty($coil_sku_id))
                {
                    $data[9] = [
                        'skuid' => $coil_sku_id,
                    ];
                }
                if (!empty($pusher_sku_id))
                {
                    $data[10] = [
                        'skuid' => $pusher_sku_id,
                    ];
                }
                if (!empty($stabiliser_sku_id))
                {
                    $data[11] = [
                        'skuid' => $stabiliser_sku_id,
                    ];
                }
                if (!empty($platform_sku_id))
                {
                    $data[12] = [
                        'skuid' => $platform_sku_id,
                    ];
                }

                if (strlen($allocation['commit_type']))
                {
                    foreach (explode(',', $allocation['commit_type']) as $index => $commit_type)
                    {
                        $attribute_id = $propertyIds[$index];

                        if (!empty($data[$attribute_id]))
                        {
                            $data[$attribute_id]['commit_type'] = $commit_type;
                        }
                        else
                        {
                            $data[$attribute_id] = ['commit_type' => $commit_type];
                        }
                    }
                }

                $allocationArray[$allocation['kiosk_id']][$allocation['position']][$status] = $data;
            }
        } 

        return $allocationArray;
    }

}
