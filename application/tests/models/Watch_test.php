<?php

class Watch_test extends TestCase {

	private static $userId;
	private static $watchId;

	public static function setUpBeforeClass() {
		$CI = &get_instance();
		$CI->load->model('User');
		$CI->load->model('Watch');
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

		$CI->Watch->delete_where(array("watchId >=" => "0"));
	}

	public function setUp() {
		$this->CI = &get_instance();
		$this->CI->load->model('Watch');
		$this->CI->load->library('Session');
		$this->session = $this->CI->session;
		$this->obj     = $this->CI->Watch;
	}

	public function test_addWatch() {

		$this->assertEquals(
			true,
			$this->obj->addWatch(
				self::$userId,
				'brand',
				'name',
				2015,
				28,
				014
			),
			'Should return true'
		);

	}

	public function test_getWatches() {

		$watches = $this->obj->getWatches(self::$userId);

		$this->assertEquals(true, is_array($watches));

		self::$watchId = $watches[0]->watchId;

		$this->assertEquals(self::$userId, $watches[0]->userId);
		$this->assertEquals('brand', $watches[0]->brand);
		$this->assertEquals('name', $watches[0]->name);
		$this->assertEquals('2015', $watches[0]->yearOfBuy);
		$this->assertEquals('28', $watches[0]->serial);
		$this->assertEquals('12', $watches[0]->caliber);
		$this->assertEquals('1', $watches[0]->status);

	}

	public function test_getWatchesUserNotExist() {
		$this->assertEquals(false, $this->obj->getWatches('42'));
	}

	public function test_getWatch() {
		$watch = $this->obj->getWatch(self::$watchId);

		$this->assertEquals(self::$userId, $watch->userId);
		$this->assertEquals('brand', $watch->brand);
		$this->assertEquals('name', $watch->name);
		$this->assertEquals('2015', $watch->yearOfBuy);
		$this->assertEquals('28', $watch->serial);
		$this->assertEquals('12', $watch->caliber);
		$this->assertEquals('1', $watch->status);
	}

	public function test_editWatch(){
		$watch = $this->obj->getWatch(self::$watchId);

		$result = $this->obj->editWatch(
			self::$userId,
			self::$watchId,
			"branda",
			"nama",
			2014,
			2013,
			2012
		);

		$watch = $this->obj->getWatch(self::$watchId);

		$this->assertEquals($result, true);

		$this->assertEquals($watch->brand, "branda");
		$this->assertEquals($watch->name, "nama");
		$this->assertEquals($watch->yearOfBuy, 2014);
		$this->assertEquals($watch->serial, 2013);
		$this->assertEquals($watch->caliber, 2012);
	}

	public function test_editWatchWrongUserId(){
		$watch = $this->obj->getWatch(self::$watchId);

		$result = $this->obj->editWatch(
			999,
			self::$watchId,
			"branda",
			"nama",
			2014,
			2013,
			2012
		);

		$watch = $this->obj->getWatch(self::$watchId);

		$this->assertEquals($result, false);
	}

	public function test_getWatchWrongId() {
		$this->assertEquals(false, $this->obj->getWatch('42'));
	}

	public function test_deleteWatch() {
		$this->assertEquals(true, $this->obj->deleteWatch(self::$watchId));
	}

	public function test_getWatchDeletedWatch() {
		$watch = $this->obj->getWatch(self::$watchId);
		$this->assertEquals(4, $watch->status);
	}

	public static function tearDownAfterClass() {
		$CI = &get_instance();
		$CI->load->model('User');
		$CI->load->model('Watch');
		$CI->Watch->delete_where(array("watchId >=" => "0"));
		$CI->User->delete_where(array("userId >="   => "0"));
	}

}

?>
