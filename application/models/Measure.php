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
	 * Retrieves the last $limit measures of an user
	 * grouped by watch
	 *
	 * @param  int $userId
	 * @param  int $limit
	 * @return array
	 */
	function getNLastMeasuresByUserByWatch($userId, $limit = 3){

		/**
		 * The following is counter-intuitive yet intended and
		 * efficient performance wise.
		 *
		 * I first select all measures right join on the watches
		 * so watches without measures pop out. Then, these rows
		 * are grouped by watch Id and mapped so we can have a nice
		 * array structure [id, brand, ..., [measures]] for serialization
		 * or display.
		 *
		 * Because of the right join, measures related part of the
		 * result row can be null, so I remove them through a reject
		 * and then, I map them with only the needed values.
		 *
		 * Performance-wise, this is way better than selecting all
		 * the non-deleted watches and for each watch, select $limit
		 * non-deleted measure because this will trigger $watch+1
		 * requests to the non-local database.
		 *
		 * Here, we only do one database request and then play around
		 * with $measures arrays.
		 */

		//we are going to use this inside callbacks
		$this->limit = $limit;

		return $this->__->reject(
				$this->__->map(
					//We group the results watchId
					$this->__->groupBy(
							// Selects all informations for all non-deleted measure
							// right join to also get non-deleted watches without measure
							$this->select('watch.watchId, watch.brand,
							watch.name, watch.yearOfBuy, watch.serial,
							watch.caliber, measure.measureUserTime, measure.id,
							measure.measureReferenceTime, measure.accuracyUserTime,
							measure.accuracyReferenceTime, measure.statusId, measure.percentile')
							->join('watch', 'measure.watchId = watch.watchId
							and measure.statusId < 4', 'right')
							->where("watch.userId", $userId)
							//exclude deleted watches
							->where("watch.status <", 4)
							//exclude uncomplete archived measures
							->where("
								(accuracyUserTime is null and 
								accuracyReferenceTime is null 
								and statusId = 3) is not ", "true", false)
							//conserving the order on which watches has been created 
							->order_by("watch.watchId")
							//reverse order as we will exclude anything which is above $limit 
							//and we want the most recent ones
							->order_by("measure.id", "desc")
							->as_array()
							->find_all(),
							'watchId'
						),
					//Mapping function starts here
					function ($watch, $row){

						// //Eleminates null measures resulting from the
						// //right join 
						$measures = $this->__->reject($watch, function($watch){
							return $watch === false || $watch['statusId'] == null;
						});

						$totalCompleteMeasures = sizeof($measures);


						//Mapping non-null measure to remove the data
						//duplicated by the group by (about the watch)
						//and remove the measures that are over $limit
						$measures = $this->__->reject(
							$this->__->map(
								$measures, function($measure, $row){

								if($row < $this->limit){
									return array(
										//The result array is explicitly typed
										//so we can json_encode this easily
										"measureUserTime"=> (double)$measure['measureUserTime'],
										"measureReferenceTime"=> (double)$measure['measureReferenceTime'],
										"accuracyUserTime"=> (double)$measure['accuracyUserTime'],
										"accuracyReferenceTime"=> (double)$measure['accuracyReferenceTime'],
										"accuracy"=> (float)$measure['accuracy'],
										"accuracyAge"=> $measure['accuracyAge'],
										"statusId"=> (float)$measure['statusId'],
										'id'=>(int)$measure["id"],
										'percentile'=>(double)$measure['percentile']
									);
								}
							}),
							//Measures above $this->limit are equal to null
							//we reject them
							function($measure){
								return $measure == null;
						});

						//Construct and return the final array
						return array(
							// Same here
							"watchId"=> ($watch[0] == false)? 0 : (int)$watch[0]["watchId"],
							"brand"=>($watch[0] == false)? 'brand' : $watch[0]["brand"],
							"name"=>($watch[0] == false)? 'name' : $watch[0]["name"],
							"yearOfBuy"=>($watch[0] == false)? 0 : (int)$watch[0]["yearOfBuy"],
							"serial"=>($watch[0] == false)? 'serial' : $watch[0]["serial"],
							"caliber"=>($watch[0] == false)? 'caliber' : $watch[0]["caliber"],
							"historySize"=>$totalCompleteMeasures,
							"measures" => $measures
							);
					// The groupBy produce one empty row if the User
					// doesn't have any watch. We remove it here.
					}), function($watch){
						return $watch["watchId"] == 0;
					});
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

		$watchMeasure->accuracy = 0.0;

		//Compute the accuracy if all the data are available
		//Both measure have been performed
		if(is_numeric($watchMeasure->accuracyUserTime)
		&& is_numeric($watchMeasure->measureUserTime)
		&& is_numeric($watchMeasure->accuracyReferenceTime)
		&& is_numeric($watchMeasure->measureReferenceTime)
		&& !is_null($watchMeasure->accuracyReferenceTime)
		&& !is_null($watchMeasure->measureReferenceTime))
		{
			$userDelta = $watchMeasure->accuracyUserTime-$watchMeasure->measureUserTime;
			$refDelta  = $watchMeasure->accuracyReferenceTime-$watchMeasure->measureReferenceTime;

			/*
			Until 1.3.0, users were asked to enter the time
			displayed on their timepiece after a 5 secs countdown.

			Since 1.3.0 users are asked to click when their Watch
			display a given time. This reverses the accuracy formulae...

			This side effect of the new measure system (https:github.com/MathieuNls/tw/issues/58)
			was reported (#136 and #137) and ignored on the basis that the test harness would have caught it.

			The following testes if the measure was taken before 1.3 - 15 fev 2016 (epoch 1455537600) (commit d861c8e436b5ea8909cd1949f86fd20a14b272b4) and adapts the formulae.
			*/
			if($watchMeasure->accuracyReferenceTime < 1455537600){

				$accuracy = ($refDelta!=0) ? ($userDelta*86400/$refDelta)-86400 : 0;
			}else{
				$accuracy = ($userDelta!=0) ? ($refDelta*86400/$userDelta)-86400 : 0;
			}
			
			$watchMeasure->unroundedAccuracy = sprintf("%.4f", $accuracy);
			$accuracy  = sprintf("%.1f", $accuracy);
			$watchMeasure->accuracy = $accuracy;

			$watchMeasure->accuracyAge =
				round((time() - $watchMeasure->accuracyReferenceTime) / 86400);
		}else{
			$watchMeasure->accuracyAge = 0;
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

		$this->notify(NEW_MEASURE,
							array('measure'   => $data));

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

		if ($this->update($measureId, $data) !== false
			&& $this->affected_rows() === 1) {

			$watchMeasure = $this
			->select("measure.*, watch.name as model, watch.brand, user.email,
				user.firstname, user.name, user.userId, ROUND(AVG(percentile), 2) as percentile")
			->join("watch", "watch.watchId = measure.watchId")
			->join("user", "user.userId = watch.userId")
			->find($measureId);

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
	 * Determine if a measure is owned by an user
	 *
	 * @param  int  $measureId
	 * @param  int  $userId
	 * @return boolean
	 */
	function isOwnedBy($measureId, $userId){
		return $this->join("watch", "watch.watchId = measure.watchId")
		->where("id", $measureId)
		->count_by("watch.userId", $userId) === 1;
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
