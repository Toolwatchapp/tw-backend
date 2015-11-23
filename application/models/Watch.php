<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

class Watch extends ObservableModel {
	function __construct() {
		parent::__construct();
		$this->table_name = "watch";
		$this->key        = "watchId";
	}

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

		$this->notify(ADD_WATCH, arrayToObject($data));

		return $res;
	}

	function getWatches($userId) {
		return $this->select()
		            ->where('watch.userId', $userId)
		            ->where('status', 1)
		            ->order_by('brand', 'asc')
		            ->find_all();

	}

	function getWatch($watchId) {
		return $this->select()->find_by("watchId", $watchId);
	}

	function getWatchByMeasureId($measureId){
		return $this->select('watch.*')
			->join('measure', 'measure.watchId = watch.watchId')
			->find_by('measure.id', $measureId);
	}

	function deleteWatch($watchId) {
		$data = array('status' => 4);
		$res  = $this->update($watchId, $data) !== false;

		$this->notify(DELETE_WATCH,
			array('user' => arrayToObject($this->session->all_userdata),
				'watch'     => $watchId));
		return $res;
	}
}
