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
	 * @param  int $userId      id of the user
	 * @return array The last measure per of $userId
	 */
	function getMeasuresByUser($userId) {

		return $this->select()
				->join("watch", "watch.watchId = measure.watchId
							AND measure.statusId < 3", "right")
				->where("watch.userId", $userId)
				->where("watch.status <", 4)
				->group_by("watch.watchId")
				->find_all();
	}

	/**
	 * Compute the accuracy of a watch given the raw data of the database
	 *
	 * @param  Measure $watchMeasure A watchMeasure object containing row data
	 * about the timing of measure
	 */
	public function computeAccuracy($watchMeasure) {

		//Some models (email model for example) require
		//data to be selected as array (mainly for grouping).
		//In the following lines, I typecast an eventual array
		//to object.
		$wasArray = false;

		if(is_array($watchMeasure)){
			$watchMeasure = (object) $watchMeasure;
			$wasArray = true;
		}

		//Compute the accuracy if all the data are available
		//Both measure have been performed
		if(is_numeric($watchMeasure->accuracyUserTime)
		&& is_numeric($watchMeasure->measureUserTime)
		&& is_numeric($watchMeasure->accuracyReferenceTime)
		&& is_numeric($watchMeasure->measureReferenceTime))
		{
			$userDelta = $watchMeasure->accuracyUserTime-$watchMeasure->measureUserTime;
			$refDelta  = $watchMeasure->accuracyReferenceTime-$watchMeasure->measureReferenceTime;
			$accuracy  = ($userDelta*86400/$refDelta)-86400;
			$accuracy  = sprintf("%.1f", $accuracy);
			$watchMeasure->accuracy = $accuracy;
		}

		//Compute 1.5 status. When a measure is less than 12 hours old
		if ($watchMeasure->statusId === "1" &&
			(((time()-$watchMeasure->measureReferenceTime)/3600) < 12)) {

			$watchMeasure->statusId         = 1.5;
			$ellapsedTime                   = ((time()-$watchMeasure->measureReferenceTime)/3600);
			$watchMeasure->accuracy         = round(12-round($ellapsedTime, 1));
			if ($watchMeasure->accuracy <= 1) {
				$watchMeasure->accuracy = " < 1";
			}
		}

		//If the measure was an array,
		//I typecast it back to array.
		if($wasArray){
			$watchMeasure = (array) $watchMeasure;
		}

		return $watchMeasure;
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
		$this->where('watchId', $watchId)
		     ->where('(`statusId` = 1 OR `statusId` = 2)', null, false)
		     ->update(null, array('statusId' => 3));

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
		return $this->join("watch", "watch.watchId = measure.watchId")
		            ->count_by("UPPER(brand)", strtoupper($watchBrand));
	}

}
