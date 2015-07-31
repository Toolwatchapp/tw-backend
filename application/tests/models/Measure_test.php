<?php

class Measure_test extends TestCase {

	private static $userId;
	private static $watchId;
	private static $measureId;
	private static $watch;
	private static $watchMeasure;

	public static function setUpBeforeClass() {
		$CI = &get_instance();
		$CI->load->model('User');
		$CI->load->model('Watch');
		$CI->load->model('Measure');
		$CI->load->library('Session');

		$CI->User->signup(
			'mathieu@gmail.com',
			'azerty',
			'math',
			'nay',
			'-5',
			'Canada'
		);

		$CI->User->login('mathieu@gmail.com', 'azerty');

		self::$userId = $CI->session->userdata('userId');

		self::$watchId = $CI->Watch->addWatch(
			self::$userId,
			'brand',
			'name',
			2015,
			28,
			014
		);

		self::$watch = $CI->Watch->getWatch(self::$watchId);

		$CI->Measure->delete_where(array("id >=" => "0"));
	}

	public function setUp() {
		$this->CI = &get_instance();
		$this->CI->load->model('Watch');
		$this->CI->load->library('Session');
		$this->CI->load->model('Measure');
		$this->obj = $this->CI->Measure;
	}

	public function test_addBaseMesure() {

		self::$measureId = $this->obj->addBaseMesure(
			self::$watchId,
			time(),
			time()
		);

		$this->assertEquals(true, is_numeric(self::$measureId));
	}

	public function test_getMeasuresByUser() {
		$measures = $this->obj->getMeasuresByUser(
			self::$userId,
			array(self::$watch)
		);

		$this->assertEquals(true, is_array($measures));
		$this->assertEquals(
			1.5,
			$measures[0]['statusId'],
			'it\'s been less than 12 hours'
		);
	}

	public function test_addAccuracyMesure() {
		$watchMeasure = $this->obj->addAccuracyMesure(
			self::$measureId,
			time()+86400000, //+1 Day
			time()+86400000
		);

		$this->assertEquals(self::$watchId, $watchMeasure->watchId);
		$this->assertEquals(0.0, $watchMeasure->accuracy, 'it should be 0.0');
		$this->assertEquals(2, $watchMeasure->statusId);
	}

	public function test_getMeasuresByUser2() {
		$measures = $this->obj->getMeasuresByUser(
			self::$userId,
			array(self::$watch)
		);

		$this->assertEquals(true, is_array($measures));

		$this->assertEquals(
			2,
			$measures[0]['statusId'],
			'Accuracy should be computed'
		);

		$this->assertEquals(
			0.0,
			$measures[0]['accuracy'],
			'it should be 0.0'
		);

	}

	/*
	start countdown : 20:52:30
	mesure : 20:52:30
	start countdown : 20:52:30 (+3 days)
	mesure : 20:52:36
	spd : +2 sec per day
	 */
	public function test_addBaseMesure1() {

		self::$measureId = $this->obj->addBaseMesure(
			self::$watchId,
			1438375950,
			1438375950
		);

		$this->assertEquals(true, is_numeric(self::$measureId));
	}

	public function test_getMeasuresByUser3() {
		$measures = $this->obj->getMeasuresByUser(
			self::$userId,
			array(self::$watch)
		);

		$this->assertEquals(true, is_array($measures));

		$this->assertEquals(
			1.5,
			$measures[0]['statusId'],
			'it\'s been less than 12 hours'
		);

	}

	public function test_addAccuracyMesure1() {
		$watchMeasure = $this->obj->addAccuracyMesure(
			self::$measureId,
			1438635150,
			1438635156
		);

		$this->assertEquals(self::$watchId, $watchMeasure->watchId);
		$this->assertEquals(2.0, $watchMeasure->accuracy, 'it should be 2.0');
		$this->assertEquals(2, $watchMeasure->statusId);
	}

	public function test_getMeasuresByUser4() {

		$measures = $this->obj->getMeasuresByUser(
			self::$userId,
			array(self::$watch)
		);

		$this->assertEquals(true, is_array($measures));

		$this->assertEquals(
			2,
			$measures[0]['statusId'],
			'Accuracy should be computed'
		);

		$this->assertEquals(
			2.0,
			$measures[0]['accuracy'],
			'it should be 2.0'
		);

	}

	/*
	start countdown : 5:30:20
	mesure : 5:31:20
	start countdown : 17:30:20 (+1.5 days)
	mesure : 17:31:26
	spd : +4 sec per day
	 */
	public function test_addBaseMesure2() {

		self::$measureId = $this->obj->addBaseMesure(
			self::$watchId,
			1438579820,
			1438579880
		);

		$this->assertEquals(true, is_numeric(self::$measureId));
	}

	public function test_getMeasuresByUser5() {
		$measures = $this->obj->getMeasuresByUser(
			self::$userId,
			array(self::$watch)
		);

		$this->assertEquals(true, is_array($measures));

		$this->assertEquals(
			1.5,
			$measures[0]['statusId'],
			'it\'s been less than 12 hours'
		);

	}

	public function test_addAccuracyMesure2() {
		$watchMeasure = $this->obj->addAccuracyMesure(
			self::$measureId,
			1438709420,
			1438709486
		);

		$this->assertEquals(self::$watchId, $watchMeasure->watchId);
		$this->assertEquals(4.0, $watchMeasure->accuracy, 'it should be 4.0');
		$this->assertEquals(2, $watchMeasure->statusId);
	}

	public function test_getMeasuresByUser6() {

		$measures = $this->obj->getMeasuresByUser(
			self::$userId,
			array(self::$watch)
		);

		$this->assertEquals(true, is_array($measures));

		$this->assertEquals(
			2,
			$measures[0]['statusId'],
			'Accuracy should be computed'
		);

		$this->assertEquals(
			4.0,
			$measures[0]['accuracy'],
			'it should be 4.0'
		);

	}

	public function test_measureArchive() {

		self::$measureId = $this->obj->addBaseMesure(
			self::$watchId,
			1438579820,
			1438579880
		);

		$this->obj->addBaseMesure(
			self::$watchId,
			1438579820,
			1438579880
		);

		$archivedMeasure = $this->obj->select()->find(self::$measureId);

		$this->assertEquals(3, $archivedMeasure->statusId);
	}

	public function test_deleteMeasure() {

		self::$measureId = $this->obj->addBaseMesure(
			self::$watchId,
			1438579820,
			1438579880
		);

		$this->obj->deleteMesure(self::$measureId);

		$deletedMeasure = $this->obj->select()->find(self::$measureId);

		$this->assertEquals(4, $deletedMeasure->statusId);
	}

	public function test_getMeasuresCountByWatchBrand() {
		$this->assertEquals(6, $this->obj->getMeasuresCountByWatchBrand('brand'));
	}

	public static function tearDownAfterClass() {
		$CI = &get_instance();
		$CI->load->model('User');
		$CI->load->model('Watch');
		$CI->load->model('Measure');

		$CI->Measure->delete_where(array("id >="    => "0"));
		$CI->Watch->delete_where(array("watchId >=" => "0"));
		$CI->User->delete_where(array("userId >="   => "0"));
	}

}

?>