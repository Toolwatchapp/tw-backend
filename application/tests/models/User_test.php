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
			'Canada'
		);

		$this->assertEquals(true, $result);

	}

	public function test_signup_fb(){
		$result = $this->obj->signup(
			'mathieu@fb.com',
			'FB_azerty',
			'math',
			'nay',
			'Canada'
		);

		$this->assertEquals(true, $result);
	}

	public function test_login_fb() {
		$result = $this->obj->login(
			'mathieu@fb.com',
			'FB_azerty'
		);

		$this->assertEquals(
			true,
			is_numeric($this->session->userdata('userId')),
			'Wrong id generated'
		);

		$this->assertNotEquals(false, $result, "Not logged");

		$this->assertEquals(
			'mathieu@fb.com',
			$this->session->userdata('email'),
			'Not good email'
		);

		$this->assertEquals(true, $this->obj->isLoggedIn());

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

		$this->assertEquals('Canada', $user->country);

		$this->assertEquals(hash('sha256', 'azerty'), $user->password);

	}

	public function test_askResetPassword() {

		$this->obj->login(
			'mathieu@gmail.com',
			'azerty'
		);
		$userId = $this->session->userdata('userId');

		//Simulate Another Session for the user that 
		//has to be deleted on password reset
		$users_session = new MY_Model("users_sessions");
		$session_model = new MY_Model('ci_sessions');

		$session_model->insert(
			array(
					'id' => '999999', 
					'ip_address'=> '192.168.1.1',
					'timestamp'=>1,
					'data'=>"adhkawjhdkd"
				)
		); 

		$users_session->insert(
				array(
					'user_id' => $userId, 
					'session_id'=> '999999'
				)
		);

		$this->assertEquals(1, $users_session->count_by('user_id', $userId));

		$user = $this->obj->getUser($userId);

		$token = $this->obj->askResetPassword($user->email);

		$user = $this->obj->getUser($userId);

		$this->assertEquals($token, $user->resetToken);
		$this->assertEquals(0, $users_session->count_by('user_id', $userId));
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

	public function test_delete(){

		$this->assertEquals(true, $this->obj->delete(self::$userId));
		$user = $this->obj->getUser(self::$userId);
		$this->assertEquals("deleted@user.com", $user->email);

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
