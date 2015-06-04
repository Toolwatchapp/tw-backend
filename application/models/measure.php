<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Measure extends MY_Model 
{
    function __construct()
    {
        parent::__construct();
        $this->table_name = "measure";
    }
    
    function getMeasuresByWatchId($watchId)
    {
        return $this->select()->find_by('watchId', $watchId);
    }

    function removeDuplicate()
    {

        $this->db->select('*')
                 ->from('watch');

        $query = $this->db->get();
        $watches = $query->result();

        foreach ($watches as $watch) {
            
            $watchMeasures = $this->select()->find_all_by('watchId', $watch->watchId);

            echo $this->db->last_query();

            var_dump($watchMeasures);

            echo '<br /><br />';

            if(sizeof($watchMeasures) % 2 === 0){

                for ($i=1; $i < sizeof($watchMeasures); $i++) { 
                       
                    $data = array(
                        'accuracyReferenceTime' => $watchMeasures[$i]->measureReferenceTime,
                        'accuracyUserTime' => $watchMeasures[$i]->measureUserTime,
                        'statusId' => 2
                    );

                    $this->update($watchMeasures[$i-1]->id, $data);
                    echo '   ' . $this->db->last_query() . '<br />';
                    $this->delete($watchMeasures[$i]->id);
                    echo '   ' . $this->db->last_query() . '<br />';

                }

            }else if(sizeof($watchMeasures) % 2 === 1 && sizeof($watchMeasures) > 2){

                for ($i=1; $i < sizeof($watchMeasures)-1; $i++) { 
                       
                    $data = array(
                        'accuracyReferenceTime' => $watchMeasures[$i]->measureReferenceTime,
                        'accuracyUserTime' => $watchMeasures[$i]->measureUserTime,
                        'statusId' => 2
                    );

                    $this->update($watchMeasures[$i-1]->id, $data);
                    echo '   ' . $this->db->last_query() . '<br />';
                    $this->delete($watchMeasures[$i]->id);
                    echo '   ' . $this->db->last_query() . '<br />';

                }

            }
        }
    }
    
    
    function getMeasuresByUser($userId, $userWatches)
    {
        
        $data = array();
        $dataPushing = 0;

        if(is_array($userWatches) && sizeof($userWatches) > 0 ){

            foreach($userWatches as $watch)
            {
                //Construct array of result
                $data[$dataPushing]['watchId'] = $watch->watchId;
                $data[$dataPushing]['brand'] = $watch->brand;
                $data[$dataPushing]['name'] = $watch->name;
                $data[$dataPushing]['yearOfBuy'] = $watch->yearOfBuy;
                $data[$dataPushing]['serial'] = $watch->serial;
                $data[$dataPushing]['statusId'] = 0;
                
                //Get measure couple that are on measure or accuracy status
                $watchMeasures = $this->select()->where('watchId', $watch->watchId)
                    ->where('(`statusId` = 1 OR `statusId` = 2)', null, false)
                    ->find_all();

                if($watchMeasures){

                    foreach ($watchMeasures as $watchMeasure) {
                        //Compute accuracy
                        if( $watchMeasure->statusId == 2 ){
                            $data[$dataPushing]['accuracy'] = sprintf("%.2f", $this->computeAccuracy($watchMeasure));
                            $data[$dataPushing]['statusId'] = $watchMeasure->statusId;
                        //Check if the measure was made less than 12 hours ago
                        } else if ( ((time() - $watchMeasure->measureReferenceTime)/3600 ) < 12 ){
                            $data[$dataPushing]['statusId'] = 1.5;
                            $watchMeasure->statusId = 1.5;
                            $ellapsedTime = ((time() - $watchMeasure->measureReferenceTime)/3600 );
                            $watchMeasure->accuracy = 12 - round($ellapsedTime, 1);
                            $data[$dataPushing]['statusId'] = $watchMeasure->statusId;
                            $data[$dataPushing]['accuracy'] = $watchMeasure->accuracy;
                        // If not, the baseMeasure is here and we are ready for the accuracy
                        } else {
                            $data[$dataPushing]['statusId'] = 1;
                        }

                        $data[$dataPushing]['measureId'] = $watchMeasure->id;
                    }   

                }

                $dataPushing++; 
            }
        }

        return $data;
    }

   private function computeAccuracy($watchMeasure){
        $userDelta = $watchMeasure->accuracyUserTime - $watchMeasure->measureUserTime + ;
        $refDelta =  $watchMeasure->accuracyReferenceTime - $watchMeasure->measureReferenceTime
        $accuracy = ($userDelta*86400/$refDelta)-86400;
        $accuracy = sprintf("%.2f", $accuracy);
        return $accuracy;
    }

    function addBaseMesure($watchId, $referenceTime, $userTime)
    {

        //Archive previous measure couples
        $data = array('statusId' => 3);

        $this->where('watchId', $watchId)
            ->where('(`statusId` = 1 OR `statusId` = 2)', null, false)
            ->update(null, $data);

        //Create new couple
        $data = array(
            'watchId' => $watchId,
            'measureReferenceTime' => $referenceTime,
            'measureUserTime' =>  $userTime,
            'statusId' => 1);
        
        return $this->insert($data);
    }

    function addAccuracyMesure($measureId, $referenceTime, $userTime){

        $data = array(
            'accuracyReferenceTime' => $referenceTime,
            'accuracyUserTime' =>  $userTime,
            'statusId' => 2);

        if($this->update($measureId, $data) !== false){

            $watchMeasure = $this->find($measureId);
            $watchMeasure->accuracy = $this->computeAccuracy($watchMeasure);

           return $watchMeasure;
        }

        return false;
        
    }

    function deleteMesure($measureId){
        
        $data = array('statusId' => 4);
        return $this->update($measureId, $data) !== false;
    }


}