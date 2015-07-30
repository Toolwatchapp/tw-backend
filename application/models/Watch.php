<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Watch extends MY_Model 
{
    function __construct()
    {
        parent::__construct();
        $this->table_name = "watch";
        $this->key = "watchId";
    }
    
    function addWatch($userId, $brand, $name, $yearOfBuy, $serial, $caliber)
    {
        $res = false;
        
        $data = array(
            'userId' => $userId,
            'brand' => $brand, 
            'name' => $name,
            'yearOfBuy' => $yearOfBuy,
            'serial' => $serial,
            'caliber' => $caliber);
        
        return $this->insert($data);
    }
    
    function getWatches($userId)
    {
        return $this->select()
                ->where('watch.userId', $userId)
                ->where('status', 1)
                ->order_by('brand', 'asc')
                ->find_all();

    }  
    
    
    function getWatch($watchId)
    {           
        return $this->select()->find_by("watchId", $watchId);
    } 

    function deleteWatch($watchId)
    {
        $data = array('status' => 4);
        return $this->update($watchId, $data) !== false;
    }
}