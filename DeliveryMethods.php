<?php

class DeliveryMethods
{
    private $_db;
    public function __construct(){


        $this->_db = new mysqli("localhost", "root", "root", "delivery_methods");

        /* check connection */
        if ($this->_db->connect_errno) {
            printf("Connect failed: %s\n", $this->_db->connect_error);
            exit();
        }

    }

    public function fetchDeliveryMethods(){

        $sql = "SELECT `id`,`name`,`value` as price,`delivery_url`,`from_weight`,`to_weight`, `notes`  FROM `method` m WHERE m.value IS NOT NULL ";
        $res = $this->_db->query($sql);

        $deliveryMethodRangeIds = array();
        $deliveryMethods = array();

        while($obj = $res->fetch_object()){

            $deliveryMethods[$obj->id]= array(
                "name" => $obj->name,
                "price" => $obj->price,
                "delivery_url" => $obj->delivery_url,
                "from_weight" => $obj->from_weight,
                "to_weight" => $obj->to_weight,
                "notes" => $obj->notes,
                "ranges" => array()
            );
            $deliveryMethodRangeIds[] = $obj->id;
        }

        if(count($deliveryMethodRangeIds) > 0) {
            $sql = "SELECT `id`, `from`, `to`, `price`,`delivery_method_id` FROM `ranges` r ".
                    "WHERE r.delivery_method_id IN (" . implode(",", $deliveryMethodRangeIds) . ")" . 
                    "ORDER BY r.order ASC";
            $res = $this->_db->query($sql);

            while ($obj = $res->fetch_object()) {
                $deliveryMethods[$obj->delivery_method_id]["ranges"][] = array("id"=>$obj->id,"from"=>$obj->from, "to"=>$obj->to, "price"=>$obj->price);
            }
        }

        return $deliveryMethods;
    }

    public function cleanPostData($data){
        $data = filter_var_array($data, FILTER_SANITIZE_STRING) ;
        return $data;
    }
    
    public function isValid(){
        return true;
    }

    public function save($data){        
        try{
            foreach($data["delivery_url"] as $key => $delivery_url) {

                if(!isset( $data['price'][$key])) continue;

                $sql = "UPDATE `method` SET `value` = '" . $data['price'][$key] . "'," .
                                                      "`delivery_url`= '" . $delivery_url . "'," .
                                                      "`from_weight`= '". $data['from_weight'][$key] . "'," .
                                                      "`to_weight`= '". $data['to_weight'][$key] ."'," .
                                                      "`notes`= '" .  $data['notes'][$key] ."'".
                                                      " WHERE `id`=" . $key;
                //echo $sql;

                $this->_db->query($sql);
            }

            foreach($data["range_price"] as $delivery_method_id => $range){
                foreach($range as $order => $range_price){

                    $id = key($range_price);

                    if($id!=-1) {     

                        $sql = "UPDATE `ranges` SET `price`='". $range_price[$id] . "',".
                                                "`from`='" . $data["range_from"][$delivery_method_id][$order][$id] . "',".
                                                "`to`='" . $data["range_to"][$delivery_method_id][$order][$id] . "',". 
                                                "`order`='" . $order. "'".
                                                " WHERE `id`=" . $id;
                    }
                    else{

                        $sql = "INSERT INTO `ranges` SET `price` = '". $range_price[$id] . "',".
                                                "`from` = '". $data["range_from"][$delivery_method_id][$order][$id] . "',".
                                                "`to` = '". $data["range_to"][$delivery_method_id][$order][$id] . "',". 
                                                "`delivery_method_id` = '". $delivery_method_id . "'," .
                                                "`order` = '". $order. "'";
                    }

                    //echo $sql;

                    $this->_db->query($sql);            
                }
            }
        }catch(Exception $e){
            return false;
        }
        
        return true;  
    }
}


$dm = new DeliveryMethods();

if(isset($_POST["save"])){
    
    $data = $dm->cleanPostData($_POST);
    
    if($dm->isValid()){
        if($dm->save($data)) {
             echo json_encode(array("success"=>true));
        }
        else{
             echo json_encode(array("error" => "Something wrong"));
        }
    }
    else{
         echo json_encode(array("error_form" => $dm->returnErrorForm()));
    }
    die();
}

$deliveryMethods = $dm->fetchDeliveryMethods();
    
//print_R($deliveryMethods);