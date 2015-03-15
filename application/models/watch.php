<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Watch extends CI_Model 
{
    function __construct()
    {
        parent::__construct();
    }
    
    function addWatch($userId, $brand, $name, $yearOfBuy, $serial)
    {
        $res = false;
        
        $data = array(
            'userId' => $userId,
            'brand' => $brand, 
            'name' => $name,
            'yearOfBuy' => $yearOfBuy,
            'serial' => $serial);
        
        $this->db->insert('watch', $data);
        
        if($this->db->affected_rows() > 0)
        {
            $res = true;
        }
        
        return $res;
    }
    
    function getWatches($userId)
    {
         $this->db->select('*')
                 ->from('watch')
                 ->where('watch.userId', $userId)
                 ->order_by('brand', 'asc');
        
        $query = $this->db->get();
        $data = $query->result();
                
        return $data;
    }    
}