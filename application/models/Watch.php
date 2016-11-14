<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

require_once('ObservableModel.php');

/**
 * Watch model. This class is responsible for handling any watch related
 * transaction.
 *
 * In addition it extends ObservableModel so it can update registered
 * observers of what's its doing.
 */
class Watch extends ObservableModel {

	/**
	 * Default constructeur
	 */
	function __construct() {
		parent::__construct();
		$this->table_name = "watch";
		//TODO: How painfull would it be to change this for a standard `id` ?
		$this->key        = "watchId";
	}

	/**
	 * Add a watch to the for $userId and according to $brand, name, $yearOfBuy,
	 * $serial and $caliber
	 *
	 * @param int $userId    id of the user adding the watch
	 * @param String $brand     Brand of the watch
	 * @param String $name      Given name of the watch
	 * @param String $yearOfBuy Year of buy for the watch
	 * @param String $serial    Serial number of the watch
	 * @param String $caliber   Caliber of the watch
	 *
	 * @return boolean|mixed The id of the row inserted or false on faillure.
	 */
	function addWatch($userId, $brand, $name, $yearOfBuy, $serial, $caliber) {
		$res = false;

		$data = array(
			'userId'    => $userId,
			'brand'     => $brand,
			'name'      => $name,
			'yearOfBuy' => $yearOfBuy,
			'serial'    => $serial,
			'caliber'   => $caliber,
			'creationDate' => time());

		$res = $this->insert($data);

		$data["watchId"] = $res;

		$this->notify(ADD_WATCH, arrayToObject($data));

		return $res;
	}

	/**
	 * Edit a watch given an userId and a watchId
	 *
	 * @param int $userId    		id of the user adding the watch
	 * @param int $watchId   		id of the watch
	 * @param String $brand     Brand of the watch
	 * @param String $name      Given name of the watch
	 * @param String $yearOfBuy Year of buy for the watch
	 * @param String $serial    Serial number of the watch
	 * @param String $caliber   Caliber of the watch
	 * @return boolean          Update results
	 */
	function editWatch($userId, $watchId, $brand, $name, $yearOfBuy, $serial, $caliber){
		$res = false;

		$data = array(
			'brand'     => $brand,
			'name'      => $name,
			'yearOfBuy' => $yearOfBuy,
			'serial'    => $serial,
			'caliber'   => $caliber
		);

		$where = array(
			'userId'    => $userId,
			'watchId'	  => $watchId
		);

		$res = $this->update($where, $data);

		$this->notify(UPDATE_WATCH, arrayToObject($data));

		return $res && $this->affected_rows() === 1;
	}

	/**
	 * Get all the watches of $userId
	 * @param  int $userId user id
	 * @return boolean|Array  An array of watches or false on faillure.
	 */
	function getWatches($userId) {
		return $this->select()
		            ->where('watch.userId', $userId)
		            ->where('status', 1)
		            ->order_by('brand', 'asc')
		            ->find_all();
	}

	/**
	 * Get watch $watchId
	 * @param  int $watchId watch id
	 * @return boolean|Watch A watch or false on faillure
	 */
	function getWatch($watchId) {
		return $this->select()->find_by("watchId", $watchId);
	}

	/**
	 * Get the watch related to $measureId
	 * @param  int $measureId The measure id of interest
	 * @return boolean|watch A watch or false on faillure
	 */
	function getWatchByMeasureId($measureId){
		return $this->select('watch.*')
			->join('measure', 'measure.watchId = watch.watchId')
			->find_by('measure.id', $measureId);
	}

	/**
	 * Checks if a watch belongs to a given suer
	 * @param  int  $watchId
	 * @param  int  $userId
	 * @return boolean
	 */
	function isOwnedBy($watchId, $userId){
		return $this->where("watchId", $watchId)
		->count_by("userId", $userId) > 0;
	}

	/**
	 * Soft delete watch $watchId
	 *
	 * @param  int $watchId The watch to delete
	 * @return boolean
	 */
	function deleteWatch($watchId, $userId) {

		$whereClause = [
			'watchId' => $watchId,
			'userId' => $userId
		];

		$data = array('status' => 4);
		$res  = $this->update($whereClause, $data) && $this->affected_rows() === 1;

		$this->notify(DELETE_WATCH,
			array('user' => arrayToObject($this->session->all_userdata()),
				'watch'     => $watchId));
		return $res;
	}
}
