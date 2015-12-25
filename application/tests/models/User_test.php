<?php

class User_test extends TestCase {

	private static $userId;

	public static function setUpBeforeClass() {
		$CI = &get_instance();
		$CI->load->model('User');

		$CI->User->delete_where(array("userId >=" => "0"));

	}

	public function setUp() {
		$this->CI = &get_instance();
		$this->CI->load->model('User');
		$this->CI->load->library('Session');
		$this->session = $this->CI->session;
		$this->obj     = $this->CI->User;
	}

	public function test_signup() {

		$result = $this->obj->signup(
			'mathieu@gmail.com',
			'azerty',
			'math',
			'nay',
			'-5',
			'Canada'
		);

		$this->assertEquals(true, $result);

	}

	public function test_login() {
		$result = $this->obj->login(
			'mathieu@gmail.com',
			'azerty'
		);

		$this->assertEquals(
			true,
			is_numeric($this->session->userdata('userId')),
			'Wrong id generated'
		);

		$this->assertNotEquals(false, $result, "Not logged");

		$this->assertEquals(
			'mathieu@gmail.com',
			$this->session->userdata('email'),
			'Not good email'
		);

		$this->assertEquals(
			'mathieu@gmail.com',
			$this->session->userdata('email'),
			'Not good email'
		);

		$this->assertEquals(true, $this->obj->isLoggedIn());

	}

	public function test_checkUserEmail() {
		$this->assertEquals(
			true,
			$this->obj->checkUserEmail('mathieu@gmail.com'),
			'Should by true'
		);

		$this->assertEquals(
			false,
			$this->obj->checkUserEmail('another@gmail.com'),
			'Should by false'
		);

	}

	public function test_logout() {

		$this->assertEquals(
			true,
			$this->obj->logout(),
			'Should by true'
		);
	}

	public function test_getUser() {

		$this->obj->login(
			'mathieu@gmail.com',
			'azerty'
		);

		$userId = $this->session->userdata('userId');

		$user = $this->obj->getUser($userId);

		$this->assertEquals('mathieu@gmail.com', $user->email);

		$this->assertEquals('math', $user->name);

		$this->assertEquals('nay', $user->firstname);

		$this->assertEquals('-5', $user->timezone);

		$this->assertEquals('Canada', $user->country);

		$this->assertEquals(hash('sha256', 'azerty'), $user->password);

	}

	public function test_askResetPassword() {

		$this->obj->login(
			'mathieu@gmail.com',
			'azerty'
		);

		$userId = $this->session->userdata('userId');

		$user = $this->obj->getUser($userId);

		$token = $this->obj->askResetPassword($user->email);

		$user = $this->obj->getUser($userId);

		$this->assertEquals($token, $user->resetToken);

	}

	public function test_resetPassword() {

		$this->obj->login(
			'mathieu@gmail.com',
			'azerty'
		);

		self::$userId = $this->session->userdata('userId');

		$user = $this->obj->getUser(self::$userId);

		$token = $this->obj->askResetPassword($user->email);

		$res = $this->obj->resetPassword($token, 'azerty');

		$this->assertEquals(true, $res);

		$user = $this->obj->getUser(self::$userId);

		$this->assertEquals(hash('sha256', 'azerty'), $user->password);

	}

	public function test_getUserFromWatchId() {

		$CI = &get_instance();
		$CI->load->model('Watch');

		$watchId = $CI->Watch->addWatch(
			self::$userId,
			'brand',
			'name',
			2015,
			28,
			014
		);

		$this->assertEquals(
			true,
			is_object($this->obj->getUserFromWatchId($watchId)),
			'should return an array'
		);
	}

	public static function tearDownAfterClass() {
		$CI = &get_instance();
		$CI->load->model('User');
		$CI->load->model('Watch');
		$CI->watch->delete_where(array("watchId >=" => "0"));
		$CI->User->delete_where(array("userId >=" => "0"));
	}

}

?>
