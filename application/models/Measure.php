<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

/**
 * Measure model.
 *
 * Measure model is responsible for all measure related transactions.
 * Also, measure model extends ObservableModel in order to send
 * information about transactions to attached observers.
 */
class Measure extends ObservableModel {

	/**
	 * Default constructeur
	 */
	function __construct() {
		parent::__construct();
		$this->table_name = "measure";

		//The computeAccuracy method will be executed
		//with each row of any fetch from the database.
		//I decided to do this (instead of storing the field in the database,
		//for example) because, if the formulae changed, we can ship a new
		//version and provide new computation of the base materials
		//(times of measure).
		//
		//The downsides are that we compute the accuracy each time the user
		//requests each dashboard and database exports are painfull if you want
		//the accuracy as you have to make sure that the formulae of the db export
		//matches this one.
		$this->after_find = array('computeAccuracy');
	}

	/**
	 * Get the last measure of each $userWatches
	 *
	 * TODO: I really don't like the overall look of this method.
	 * We should return an array of $watchMeasure and get rid of $dataPushing.
	 * I think data pushing date back from when measures 1/2 and 2/2 were not
	 * a couple but two instances of the same db objects.
	 *
	 * @param  int $userId      id of the user
	 * @param  array $userWatches watches of the user
	 * @return array The last measure per $userWatches for $userId
	 */
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
							$data[$dataPushing]['accuracy'] = $watchMeasure->accuracy;
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

	/**
	 * Compute the accuracy of a watch given the raw data of the database
	 * @param  Measure $watchMeasure A watchMeasure object containing row data
	 * about the timing of measure
	 */
	public function computeAccuracy(&$watchMeasure) {
		$userDelta = $watchMeasure->accuracyUserTime-$watchMeasure->measureUserTime;
		$refDelta  = $watchMeasure->accuracyReferenceTime-$watchMeasure->measureReferenceTime;
		$accuracy  = ($userDelta*86400/$refDelta)-86400;
		$accuracy  = sprintf("%.1f", $accuracy);
		$watchMeasure->accuracy = $accuracy;
	}

	/**
	 * Add a base measure (1/2) to $watchId given $referenceTime and $userTime
	 *
	 * All previous measures, completed or not, will be archived (status = 3)
	 * at the creation of a new measure.
	 *
	 * @param int $watchId       The watch being mesured
	 * @param Long $referenceTime the reference time in ms
	 * @param Long $userTime      the user time in ms
	 */
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

	/**
	 * Add an accuracy measure (2/2) for $measureId given $referenceTime and
	 * $userTime
	 *
	 * @param [type] $measureId     [description]
	 * @param [type] $referenceTime [description]
	 * @param [type] $userTime      [description]
	 *
	 * @return mixed|boolean The new accuracy measure.
	 */
	function addAccuracyMesure($measureId, $referenceTime, $userTime) {

		$data = array(
			'accuracyReferenceTime' => $referenceTime,
			'accuracyUserTime'      => $userTime,
			'statusId'              => 2);

		if ($this->update($measureId, $data) !== false) {

			$watchMeasure = $this->find($measureId);

			$this->notify(NEW_ACCURACY,
				array('measure'   => $watchMeasure));

			return $watchMeasure;
		}

		return false;

	}

	/**
	 * (Soft) Delete the $measureId measure.
	 *
	 * @param  int $measureId MeasureId of the measure to be deleted
	 * @return boolean
	 */
	function deleteMesure($measureId) {

		$data = array('statusId' => 4);

		$this->notify(DELETE_MEASURE,
			array('user' => arrayToObject($this->session->all_userdata()),
				'measure'   => $measureId));

		return $this->update($measureId, $data) !== false;
	}

	/**
	 * Count the amount of watch of $watchBrand
	 *
	 * @param  String $watchBrand The watchBrand of interest
	 * @return int How many watches belong tp $watchBrand
	 */
	function getMeasuresCountByWatchBrand($watchBrand) {
		return $this->select("count(1) as cnt")
		            ->join("watch", "watch.watchId = measure.watchId")
		            ->find_by("UPPER(brand)", strtoupper($watchBrand))
		            ->cnt;
	}

}
