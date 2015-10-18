<?php

class DeliveryMethods
{
    private $_db;
    public function __construct(){


        $this->_db = new mysqli("localhost", "root", "root", "delivary_methods");

        /* check connection */
        if ($this->_db->connect_errno) {
            printf("Connect failed: %s\n", $this->_db->connect_error);
            exit();
        }

    }

    public function fetchDeliveryMethods(){

        $sql = "SELECT `id`,`name`,`value` as price,`delivery_url`,`from_weight`,`to_weight`, `notes`  FROM `method` m WHERE m.value IS NOT NULL ";
        $res = $this->_db->query($sql);

        $delivaryMethodRangeIds = array();
        $delivaryMethods = array();

        while($obj = $res->fetch_object()){

            $delivaryMethods[$obj->id]= array(
                "name" => $obj->name,
                "price" => $obj->price,
                "delivery_url" => $obj->delivery_url,
                "from_weight" => $obj->from_weight,
                "to_weight" => $obj->to_weight,
                "notes" => $obj->notes,
                "ranges" => array()
            );
            $delivaryMethodRangeIds[] = $obj->id;
        }

        if(count($delivaryMethodRangeIds) > 0) {
            $sql = "SELECT `id`, `from`, `to`, `price`,`delivary_method_id` FROM `ranges` r WHERE r.delivary_method_id IN (" . implode(",", $delivaryMethodRangeIds) . ")";
            $res = $this->_db->query($sql);

            while ($obj = $res->fetch_object()) {
                $delivaryMethods[$obj->delivary_method_id]["ranges"][] = array("id"=>$obj->id,"from"=>$obj->from, "to"=>$obj->to, "price"=>$obj->price);
            }
        }

        return $delivaryMethods;
    }
    private function _cleanPostData($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public function saveDeliveryMethods(){
        $data = $this->_cleanPostData($_POST);
        foreach($data as $delivery_methods) {
            $sql = "UPDATE `method` SET `value` = ''" . $data['price'] . "'," .
                                                  "`delivery_url`= '" . $data['delivery_url'] . "'," .
                                                  "`from_weight`= '". $data['from_weight'] . "'," .
                                                  "`to_weight`= '". $data['to_weight'] ."'," .
                                                  "`notes`= '" .  $data['notes'] ."'".
                                                  "WHERE m.`id` = " . $delivery_methods['id'];
        }
        $res = $this->_db->query($sql);

    }
}


    $dm = new DeliveryMethods();

    if(isset($_POST["save"])){

        $dm->saveDeliveryMethods();
    }

    $delivaryMethods = $dm->fetchDeliveryMethods();
//print_R($delivaryMethods);