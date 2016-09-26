<?php

class Measure_test extends TestCase {

	private static $userId;
	private static $watchId;
	private static $watchId2;
	private static $watchId3;
	private static $measureId;
	private static $watch;
	private static $watchMeasure;

	public static function setUpBeforeClass() {
		$CI = &get_instance();
		$CI->load->model('User');
		$CI->load->model('Watch');
		$CI->load->model('Measure');
		$CI->load->library('Session');

		$CI->watch->delete_where(array("watchId >=" => "0"));
		$CI->User->delete_where(array("userId >=" => "0"));
		$CI->Measure->delete_where(array("id >=" => "0"));

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
			'name1',
			2015,
			28,
			014
		);

		self::$watchId2 = $CI->Watch->addWatch(
			self::$userId,
			'brand',
			'name2',
			2015,
			28,
			014
		);

		self::$watchId3 = $CI->Watch->addWatch(
			self::$userId,
			'brand',
			'name3',
			2015,
			28,
			014
		);

		self::$watch = $CI->Watch->getWatch(self::$watchId);
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

		$this->assertEquals(
			true,
			is_numeric(
				$this->obj->addBaseMesure(
					self::$watchId2,
					time()-11*60*60,
					time()-11*60*60
				)
			)
		);
	}

	public function test_getMeasuresByUser() {
		$measures = $this->obj->getMeasuresByUser(
			self::$userId);

		$this->assertEquals(true, is_array($measures));
		$this->assertEquals(
			1.5,
			$measures[0]->statusId,
			'it\'s been less than 12 hours'
		);

		$this->assertEquals(
			1.5,
			$measures[1]->statusId,
			'it\'s been less than 12 hours'
		);

		$this->assertEquals(
			" < 1",
			$measures[1]->accuracy,
			'Testable in less than one hours'
		);
	}

	public function test_addAccuracyMesure() {

		$this->assertEquals(false,
		$this->obj->addAccuracyMesure(
			1,
			time()+86400000, //+1 Day
			time()+86400000
		), 'This measure does not exist');

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
			$measures[0]->statusId,
			'Accuracy should be computed'
		);

		$this->assertEquals(
			0.0,
			$measures[0]->accuracy,
			'it should be 0.0'
		);

	}

	/*
	start countdown : 20:52:30
	mesure : 20:52:30
	start countdown : 20:52:30 (+3 days)
	mesure : 20:52:36
	spd : -2 sec per day
	@before 1.3
	 */
	public function test_addBaseMesure1() {

		self::$measureId = $this->obj->addBaseMesure(
			self::$watchId,
			1438375950,
			1438375950
		);

		$this->assertEquals(true, is_numeric(self::$measureId));
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
			$measures[0]->statusId,
			'Accuracy should be computed'
		);

		$this->assertEquals(
			2.0,
			$measures[0]->accuracy,
			'it should be 2.0'
		);

	}

	/*
	start countdown : 5:30:20
	mesure : 5:31:20
	start countdown : 17:30:20 (+1.5 days)
	mesure : 17:31:26
	spd : +4 sec per day
	@before 1.3
	 */
	public function test_addBaseMesure2() {

		self::$measureId = $this->obj->addBaseMesure(
			self::$watchId,
			1438579820,
			1438579880
		);

		$this->assertEquals(true, is_numeric(self::$measureId));
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

	/*
	start countdown : 09:08:01
	mesure : 09:08:01
	start countdown : 09:08:00 (+1 day)
	mesure : 09:07:55
	spd : +5 sec per day
	@after 1.3
	 */
	public function test_addBaseMesure3() {

		self::$measureId = $this->obj->addBaseMesure(
			self::$watchId3,
			1458634081,
			1458634081
		);

		$this->assertEquals(true, is_numeric(self::$measureId));
	}

	public function test_addAccuracyMesure3() {
		$watchMeasure = $this->obj->addAccuracyMesure(
			self::$measureId,
			1458720480,
			1458720475
		);

		$this->assertEquals(self::$watchId3, $watchMeasure->watchId);
		$this->assertEquals(5.0, $watchMeasure->accuracy, 'it should be 5.0');
		$this->assertEquals(2, $watchMeasure->statusId);
	}

		/*
	start countdown : 09:08:01
	mesure : 09:08:01
	start countdown : 09:08:00 (+1 day)
	mesure : 09:07:55
	spd : +5 sec per day
	@after 1.3
	 */
	public function test_addBaseMesure4() {

		self::$measureId = $this->obj->addBaseMesure(
			self::$watchId,
			1458634081,
			1458634081
		);

		$this->assertEquals(true, is_numeric(self::$measureId));
	}

	public function test_addAccuracyMesure4() {
		$watchMeasure = $this->obj->addAccuracyMesure(
			self::$measureId,
			1458720480,
			1458720475
		);

		$this->assertEquals(self::$watchId, $watchMeasure->watchId);
		$this->assertEquals(5.0, $watchMeasure->accuracy, 'it should be 5.0');
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
			$measures[0]->statusId,
			'Accuracy should be computed'
		);

		$this->assertEquals(
			5.0,
			$measures[0]->accuracy,
			'it should be 5.0'
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

	public function test_getNLastMeasuresByUserByWatch(){


		$measures = $this->obj->getNLastMeasuresByUserByWatch(
			self::$userId);

		$this->assertEquals(3, sizeof($measures), "User have 3 watches");
		$this->assertEquals(3, sizeof($measures[0]['measures']), "First watches have 3 measures");
		$this->assertEquals(5, $measures[0]['historySize'], "First watches have 2 hidden measures");
		$this->assertEquals(100, $measures[0]['measures'][0]['percentile'], "First watches have a 100% percentile for the first measure");
		$this->assertEquals(1, sizeof($measures[1]['measures']), "Second Watch have 1 measure");
		$this->assertEquals(1, $measures[1]['historySize'], "Second watches have 0 hidden measures");

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
		$count = $this->obj->getMeasuresCountByWatchBrand('brand');
		$this->assertEquals(9, $count);
	}

	// public static function tearDownAfterClass() {
	// 	$CI = &get_instance();
	// 	$CI->load->model('User');
	// 	$CI->load->model('Watch');
	// 	$CI->load->model('Measure');
	// 	$CI->watch->delete_where(array("watchId >=" => "0"));
	// 	$CI->User->delete_where(array("userId >=" => "0"));
	// 	$CI->Measure->delete_where(array("id >=" => "0"));
	// }

}

?>
