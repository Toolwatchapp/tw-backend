<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Measure extends CI_Model 
{
    function __construct()
    {
        parent::__construct();
    }
    
    /*function getMeasure($userId)
    {
        $this->db->select('watch.*, measure.measureId, measure.referenceTime, measure.userTime')
                 ->from('watch, measure')
                 //->where('`watch`.`watchId`=`measure`.`watchId`')
                 ->where('watch.userId', $userId);
        
        $query = $this->db->get();
        $data = $query->result();
                
        return $data;
    }*/
    
    function getMeasures($watchId)
    {
        $this->db->select('*')
                 ->from('measure')
                 ->where('watchId', $watchId);
        
        $query = $this->db->get();
        $data = $query->result();
                
        return $data;
    }
    
    
    function computeMeasure($userId)
    {
        $userWatches = $this->watch->getWatches($userId);
        
        $data = array();
        $savedWatchId = 0;
        $dataPushing = 0;
        
        foreach($userWatches as $watch)
        {
        
            // Save of the watch data
            $data[$dataPushing]['watchId'] = $watch->watchId;
            $data[$dataPushing]['brand'] = $watch->brand;
            $data[$dataPushing]['name'] = $watch->name;
            $data[$dataPushing]['yearOfBuy'] = $watch->yearOfBuy;
            $data[$dataPushing]['serial'] = $watch->serial;
            
            // Getting watch accuracy
            $accuracy = $this->getWatchAccuracy($watch->watchId);
            if((strcmp($accuracy, 'newMeasure') == 0) || (strcmp($accuracy, 'getAccuracy') == 0))
            {
                $data[$dataPushing]['accuracy'] = $accuracy;
            }
            else
            {               
                $data[$dataPushing]['accuracy'] = sprintf("%.1f", $accuracy);
            }            
            
            $dataPushing++;  
        }

        return $data;
    }
    
    function newMeasure($watchId, $referenceTime, $userTime)
    {
        $res = false;
        
        $data = array(
            'watchId' => $watchId,
            'referenceTime' => $referenceTime,
            'userTime' =>  $userTime);
        
        $this->db->insert('measure', $data);
        
        if($this->db->affected_rows() > 0)
        {
            $res = true;
        }
        
        return $res;
    }
    
    function getWatchAccuracy($watchId)
    {
        $accuracy = 0;
        $watchMeasures = $this->getMeasures($watchId); 
        
        // Reset of deltas
        $savedRefTime = array();
        $savedUserTime = array();
        $refDelta = 0;
        $userDelta = 0;
            
        // Now, we need to compute the data
        for($i=0; $i < sizeof($watchMeasures); $i++)
        {
            $savedRefTime[$i] = $watchMeasures[$i]->referenceTime;
            $savedUserTime[$i] = $watchMeasures[$i]->userTime;
            
            if($i > 0)
            {            
                $refDelta += $savedRefTime[$i] - $savedRefTime[$i-1];
                $userDelta += $savedUserTime[$i] - $savedUserTime[$i-1];
            } 
        }
        
        if(sizeof($watchMeasures) > 1)
        {
            $accuracy = ($userDelta*86400/$refDelta)-86400;
            $accuracy = floor($accuracy*10.0)/10.0;
           
        }
        else if(sizeof($watchMeasures) == 1)
        {
            $accuracy = 'getAccuracy';
        }
        else
        {
            $accuracy = 'newMeasure';
        }
        
        return $accuracy;
    }
}