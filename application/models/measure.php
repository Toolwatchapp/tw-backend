<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Measure extends CI_Model 
{
    function __construct()
    {
        parent::__construct();
    }
    
    function getMeasure($userId)
    {
        $this->db->select('watch.*, measure.measureId, measure.referenceTime, measure.userTime')
                 ->from('watch, measure')
                 ->where('`watch`.`watchId`=`measure`.`watchId`')
                 ->where('watch.userId', $userId);
        
        $query = $this->db->get();
        $data = $query->result();
                
        return $data;
    }
    
    function computeMeasure($userId)
    {
        $rawData = $this->getMeasure($userId); 
        
        $data = array();
        $currentWatchId = 0;
        $savedWatchId = 0;
        $dataPushing = 0;
        
        $i = 0;
        $j = 0;
        
        while($i < sizeof($rawData))
        {
            // Reset of deltas
            $savedRefTime = array();
            $savedUserTime = array();
            $refDelta = 0;
            $userDelta = 0;
            
            // Get the watch id
            $currentWatchId =  $rawData[$i]->watchId;
            //echo $currentWatchId.'<br>';
            
            // Save of the watch data
            $data[$dataPushing]['brand'] = $rawData[$i]->brand;
            $data[$dataPushing]['name'] = $rawData[$i]->name;
            $data[$dataPushing]['yearOfBuy'] = $rawData[$i]->yearOfBuy;
            $data[$dataPushing]['serial'] = $rawData[$i]->serial;
            
            // Now, we need to compute the data
            while(($i+$j < sizeof($rawData)) && ($rawData[$i+$j]->watchId == $currentWatchId))
            {
                $savedRefTime[$j] = $rawData[$i+$j]->referenceTime;
                $savedUserTime[$j] = $rawData[$i+$j]->userTime;
                //echo $savedRefTime[$j].' - '.$savedUserTime[$j].'<br>';
                
                if($j > 0)
                {            
                    $refDelta += $savedRefTime[$j] - $savedRefTime[$j-1];
                    $userDelta += $savedUserTime[$j] - $savedUserTime[$j-1];
                }
                
                $j++;
            }
            
            if($j > 1)
            {
                $val = ($userDelta*86400/$refDelta)-86400;
                $val = floor($val*10.0)/10.0;
            }
            else
            {
                $val = 0;
            }
            
            $data[$dataPushing]['accuracy'] = sprintf("%.1f", $val);;
            
            $dataPushing++;  
            $i += $j;
            $j=0;
        }
        
        
        //var_dump($data);
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
}