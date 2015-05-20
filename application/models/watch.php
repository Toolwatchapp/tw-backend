<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Watch extends CI_Model 
{
    function __construct()
    {
        parent::__construct();
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
    
    
    function getWatch($watchId)
    {
         $this->db->select('*')
                 ->from('watch')
                 ->where('watchId', $watchId);
        
        $query = $this->db->get();
        $data = $query->result();
                
        return $data[0];
    } 
    
    function deleteMeasures($watchId)
    {
        $res = false;
        
        $this->db->delete('measure', array('watchId' => $watchId));    
        
        if($this->db->affected_rows() > 0)
        {
            $res = true;
        }
        
        return $res;
    }
    
    function deleteWatch($watchId)
    {
        $res = false;
        
        /*if($this->deleteMeasures($watchId))
        {
            $this->db->delete('watch', array('watchId' => $watchId));    
            
            if($this->db->affected_rows() > 0)
            {
                $res = true;
            }
        }*/
        
        $this->db->delete('watch', array('watchId' => $watchId));    
            
        if($this->db->affected_rows() > 0)
        {
            $res = true;
        }
        
        return $res;
    }
}