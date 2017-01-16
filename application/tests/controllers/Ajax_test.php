<?php

class Ajax_test extends TestCase {

	private static $watchId;
	private static $userId;

	public static function setUpBeforeClass() {
		$CI = &get_instance();
		$CI->load->model('User');
		$CI->load->model('Measure');
		$CI->load->model('Watch');
		$CI->User->delete_where(array("userId >="   => "0"));
		$CI->Measure->delete_where(array("id >="    => "0"));
		$CI->Watch->delete_where(array("watchId >=" => "0"));
	}

	public function test_reportClientError(){
		$output = $this->request(
			'POST',
			['Ajax', 'reportClientError'],
			[
				'error' => 'A test error from a test user'
			]
		);

		$this->assertResponseCode(200);
	}

	public function test_checkEmail() {
		$output = $this->request(
			'POST',
			['Ajax', 'checkEmail'],
			[
				'email' => 'mathieu@gmail.com'
			]
		);

		$this->assertContains('{"success":true}', $output);
	}

	public function test_signup() {

		$output = $this->request(
			'POST',
			['Ajax', 'signup'],
			[
				'email'       => 'mathieu@gmail.com',
				'password'    => 'password',
				'name'        => 'name',
				'firstname'   => 'firstname',
				'country'     => 'country',
				'mailingList' => 'false',
			]
		);

		$this->assertContains('"success":true', $output);
	}

	public function test_checkEmailFail() {
		$output = $this->request(
			'POST',
			['Ajax', 'checkEmail'],
			[
				'email' => 'mathieu@gmail.com'
			]
		);

		$this->assertContains('{"success":false}', $output);
	}

	public function test_login() {
		$output = $this->request(
			'POST',
			['Ajax', 'login'],
			[
				'email'    => 'mathieu@gmail.com',
				'password' => 'password'
			]
		);

		$this->assertContains('{"success":true}', $output);
	}

	public function test_loginFail() {
		$output = $this->request(
			'POST',
			['Ajax', 'login'],
			[
				'email'    => 'mathieu@gmail.com',
				'password' => 'password2'
			]
		);

		$this->assertContains('{"success":false}', $output);
	}

	public function test_loginFailFb() {
		$output = $this->request(
			'POST',
			['Ajax', 'login'],
			[
				'email'    => 'mathieu@gmail.com',
				'password' => 'FB_sqdq'
			]
		);

		$this->assertContains('{"success":false}', $output);
	}

	public function test_signupFail() {

		$output = $this->request(
			'POST',
			['Ajax', 'signup'],
			[
				'email'       => 'mathieu@gmail.com',
				'password'    => 'password',
				'name'        => 'name',
				'firstname'   => 'firstname',
				'country'     => 'country',
				'mailingList' => 'false',
			]
		);

		$this->assertContains('email', $output);
	}

	public function test_facebookSignup() {

		Facebook::$is_valid = true;

		$output = $this->request(
			'POST',
			['Ajax', 'facebookSignup'],
			[
				'email'       => 'mathieu_fb@gmail.com',
				'token'       => '10',
				'lastname'    => 'name',
				'firstname'   => 'firstname'
			]
		);

		$this->assertContains('signup', $output);

		Facebook::$is_valid = false;

	}

	public function test_facebookSignin() {
		Facebook::$is_valid = true;
		$output = $this->request(
			'POST',
			['Ajax', 'facebookSignup'],
			[
				'email'       => 'mathieu_fb@gmail.com',
				'token'          => '10',
				'lastname'   => 'name',
				'firstname'   => 'firstname',
				'country'     => 'country',
				'mailingList' => 'false',
			]
		);

		$this->assertContains('signin', $output);
		Facebook::$is_valid = false;

	}

	public function test_facebookSigninFalse() {

		$output = $this->request(
			'POST',
			['Ajax', 'facebookSignup'],
			[
				'email'       => 'mathieu_fb@gmail.com',
				'token'          => '11',
				'lastname'   => 'name',
				'firstname'   => 'firstname',
				'country'     => 'country',
				'mailingList' => 'email',
			]
		);

		$this->assertContains('email', $output);

	}

	public function test_askResetPassword() {

		$output = $this->request(
			'POST',
			['Ajax', 'askResetPassword'],
			[
				'email' => 'mathieu@gmail.com'
			]
		);

		$this->assertContains('true', $output);
	}

	public function test_askResetPasswordFail() {
		$output = $this->request(
			'POST',
			['Ajax', 'askResetPassword'],
			[
				'email' => 'qzdqdqd@gmail.com'
			]
		);

		$this->assertContains('false', $output);
	}

	public function test_resetPassordFail() {

		$output = $this->request(
			'POST',
			['Ajax', 'resetPassword'],
			[
				'resetToken' => 'ab',
				'password' => 'abcd'
			]
		);

		$this->assertContains('false', $output);
	}

	public function test_resetPassword() {

		$CI = &get_instance();
		$CI->load->model('User');
		$user = $CI->User->find_by('email', 'mathieu@gmail.com');

		self::$userId = $user->userId;

		$output = $this->request(
			'POST',
			['Ajax', 'resetPassword'],
			[
				'resetToken' => $user->resetToken,
				'password' => 'abcd'
			]
		);

		$this->assertContains('true', $output);
	}

	public function test_contact(){
		$output = $this->request(
			'POST',
			['Ajax', 'contact'],
			[
				'name' => 'Mathieu',
				'email' => 'mathieu@gmail.com',
				'message' => 'hello'
			]
		);

		$this->assertContains('true', $output);
	}
}
