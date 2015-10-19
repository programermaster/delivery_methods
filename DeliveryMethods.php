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
            $sql = "SELECT `id`, `from`, `to`, `price`,`delivary_method_id` FROM `ranges` r ".
                    "WHERE r.delivary_method_id IN (" . implode(",", $delivaryMethodRangeIds) . ")" . 
                    "ORDER BY r.order ASC";
            $res = $this->_db->query($sql);

            while ($obj = $res->fetch_object()) {
                $delivaryMethods[$obj->delivary_method_id]["ranges"][] = array("id"=>$obj->id,"from"=>$obj->from, "to"=>$obj->to, "price"=>$obj->price);
            }
        }

        return $delivaryMethods;
    }
    private function _cleanPostData($data){
        $data = htmlspecialchars(stripslashes(trim($data)));
        return $data;
    }

    public function save(){
        $data = $this->_cleanPostData($_POST);
        
        foreach($data["delivery_url"] as $key => $delivery_method) {
            $sql = "UPDATE `method` SET `value` = ''" . $delivery_method['price'] . "'," .
                                                  "`delivery_url`= '" . $delivery_method['delivery_url'] . "'," .
                                                  "`from_weight`= '". $delivery_method['from_weight'] . "'," .
                                                  "`to_weight`= '". $delivery_method['to_weight'] ."'," .
                                                  "`notes`= '" .  $delivery_method['notes'] ."'".
                                                  " WHERE `id`=" . $delivery_method['id'];
        
            $this->_db->query($sql);
        }
        
      
        foreach($data["range_price"] as $key => $range){
            
            $price = value($range);
            
            if($price!=-1) {     
              
                $sql = "UPDATE `range` SET `price`='". $price . "',".
                                        "`from`='" . value($data["range_from"][$key]) . "',".
                                        "`to`='" . value($data["range_from"][$key]) . "',". 
                                        "`order`='" . $key. "',".
                                        " WHERE `id`=" . key($range); 
            }
            else{
                $sql = "INSERT INTO `range` SET `price` = '". $price . "',".
                                        "`from` = '". value($data["range_from"][$key]) . "',".
                                        "`to` = '".value($data["range_from"][$key]) . "',". 
                                        "`order` = '". $key. "'";
                                        
            }
            
            $this->_db->query($sql);            
        }                
    }
}


$dm = new DeliveryMethods();

if(isset($_POST["save"])){
    $dm->save();
}

$delivaryMethods = $dm->fetchDeliveryMethods();
    
//print_R($delivaryMethods);