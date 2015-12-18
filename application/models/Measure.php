<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

class Measure extends ObservableModel {

	function __construct() {
		parent::__construct();
		$this->table_name = "measure";
		$this->after_find = array('computeAccuracy');
	}

	function getMeasuresByUser($userId, $userWatches) {

		$data        = array();
		$dataPushing = 0;

		if (is_array($userWatches) && sizeof($userWatches) > 0) {

			foreach ($userWatches as $watch) {
				//Construct array of result
				$data[$dataPushing]['watchId']   = $watch->watchId;
				$data[$dataPushing]['brand']     = $watch->brand;
				$data[$dataPushing]['name']      = $watch->name;
				$data[$dataPushing]['yearOfBuy'] = $watch->yearOfBuy;
				$data[$dataPushing]['serial']    = $watch->serial;
				$data[$dataPushing]['statusId']  = 0;

				//Get measure couple that are on measure or accuracy status
				$watchMeasures = $this->select()->where('watchId', $watch->watchId)
					->where('(`statusId` = 1 OR `statusId` = 2)', null, false)
					->find_all();

				if ($watchMeasures) {

					foreach ($watchMeasures as $watchMeasure) {
						//Compute accuracy
						if ($watchMeasure->statusId == 2) {
							$data[$dataPushing]['accuracy'] = $watchMeasure->accuracy;//sprintf("%.1f", $this->computeAccuracy($watchMeasure));
							$data[$dataPushing]['statusId'] = $watchMeasure->statusId;
							//Check if the measure was made less than 12 hours ago
						} else if (((time()-$watchMeasure->measureReferenceTime)/3600) < 12) {
							$data[$dataPushing]['statusId'] = 1.5;
							$watchMeasure->statusId         = 1.5;
							$ellapsedTime                   = ((time()-$watchMeasure->measureReferenceTime)/3600);
							$watchMeasure->accuracy         = round(12-round($ellapsedTime, 1));
							if ($watchMeasure->accuracy <= 1) {
								$watchMeasure->accuracy = " < 1";
							}
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

	public function computeAccuracy(&$watchMeasure) {
		$userDelta = $watchMeasure->accuracyUserTime-$watchMeasure->measureUserTime;
		$refDelta  = $watchMeasure->accuracyReferenceTime-$watchMeasure->measureReferenceTime;
		$accuracy  = ($userDelta*86400/$refDelta)-86400;
		$accuracy  = sprintf("%.1f", $accuracy);
		$watchMeasure->accuracy = $accuracy;
		return $watchMeasure;
	}

	function addBaseMesure($watchId, $referenceTime, $userTime) {

		//Archive previous measure couples
		$data = array('statusId' => 3);

		$this->where('watchId', $watchId)
		     ->where('(`statusId` = 1 OR `statusId` = 2)', null, false)
		     ->update(null, $data);

		//Create new couple
		$data = array(
			'watchId'              => $watchId,
			'measureReferenceTime' => $referenceTime,
			'measureUserTime'      => $userTime,
			'statusId'             => 1);

		$returnValue = $this->insert($data);

		return $returnValue;
	}

	function addAccuracyMesure($measureId, $referenceTime, $userTime) {

		$data = array(
			'accuracyReferenceTime' => $referenceTime,
			'accuracyUserTime'      => $userTime,
			'statusId'              => 2);

		if ($this->update($measureId, $data) !== false) {

			$watchMeasure           = $this->find($measureId);
			//$watchMeasure->accuracy = $this->computeAccuracy($watchMeasure);

			$this->notify(NEW_ACCURACY,
				array('measure'   => $watchMeasure));

			return $watchMeasure;
		}

		return false;

	}

	function deleteMesure($measureId) {

		$data = array('statusId' => 4);

		$this->notify(DELETE_MEASURE,
			array('user' => arrayToObject($this->session->all_userdata()),
				'measure'   => $measureId));

		return $this->update($measureId, $data) !== false;
	}

	function getMeasuresCountByWatchBrand($watchBrand) {
		return $this->select("count(1) as cnt")
		            ->join("watch", "watch.watchId = measure.watchId")
		            ->find_by("UPPER(brand)", strtoupper($watchBrand))
		            ->cnt;
	}

}
