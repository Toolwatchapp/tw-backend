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
				'timezone'    => 'timezone',
				'country'     => 'country',
				'mailingList' => 'false',
			]
		);

		$this->assertContains('{"success":true}', $output);
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

	public function test_signupFail() {

		$output = $this->request(
			'POST',
			['Ajax', 'signup'],
			[
				'email'       => 'mathieu@gmail.com',
				'password'    => 'password',
				'name'        => 'name',
				'firstname'   => 'firstname',
				'timezone'    => 'timezone',
				'country'     => 'country',
				'mailingList' => 'false',
			]
		);

		$this->assertContains('email', $output);
	}

	public function test_facebookSignup() {
		$output = $this->request(
			'POST',
			['Ajax', 'facebookSignup'],
			[
				'email'       => 'mathieu_fb@gmail.com',
				'id'          => '10',
				'name'        => 'name',
				'firstname'   => 'firstname',
				'timezone'    => 'timezone',
				'country'     => 'country',
				'mailingList' => 'false',
			]
		);

		$this->assertContains('signup', $output);

	}

	public function test_facebookSignin() {
		$output = $this->request(
			'POST',
			['Ajax', 'facebookSignup'],
			[
				'email'       => 'mathieu_fb@gmail.com',
				'id'          => '10',
				'name'        => 'name',
				'firstname'   => 'firstname',
				'timezone'    => 'timezone',
				'country'     => 'country',
				'mailingList' => 'false',
			]
		);

		$this->assertContains('signin', $output);

	}

	public function test_facebookSigninFalse() {
		$output = $this->request(
			'POST',
			['Ajax', 'facebookSignup'],
			[
				'email'       => 'mathieu_fb@gmail.com',
				'id'          => '11',
				'name'        => 'name',
				'firstname'   => 'firstname',
				'timezone'    => 'timezone',
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

			]
		);

		$this->assertContains('false', $output);
	}

	public function test_resetPassordFail() {

		$output = $this->request(
			'POST',
			['Ajax', 'resetPassword'],
			[
				'resetToken' => 'ab'
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
				'resetToken' => $user->resetToken
			]
		);

		$this->assertContains('true', $output);
	}

	public function test_getReferenceTime() {
		$CI = &get_instance();
		$CI->load->model('User');
		$CI->load->library('Session');

		$output = $this->request('GET', ['Ajax', 'getReferenceTime']);

		$this->assertEquals($CI->session->userdata('referenceTime'), time());
	}

	public function test_baseMesure() {

		$CI = &get_instance();
		$CI->load->model('User');
		$CI->load->model('Watch');
		$CI->load->library('Session');

		$CI->session->set_userdata('referenceTime', time());

		self::$watchId = $CI->Watch->addWatch(
			self::$userId,
			'brand',
			'name',
			2015,
			28,
			014
		);

		$output = $this->request(
			'POST',
			['Ajax', 'baseMeasure'],
			[
				'watchId'      => self::$watchId,
				'userTime'     => '10:13:12',
				'userTimezone' => '5'
			]
		);

		$this->assertContains('true', $output);

	}

	public function test_accuracyMeasure() {
		$CI = &get_instance();
		$CI->load->model('Measure');
		$measure = $CI->Measure->find_by('watchId', self::$watchId);

		$output = $this->request(
			'POST',
			['Ajax', 'accuracyMeasure'],
			[
				'measureId'    => $measure->id,
				'userTime'     => '10:16:12',
				'userTimezone' => '5'
			]
		);

		$this->assertContains('true', $output);

	}

	public function test_contact() {

		$output = $this->request(
			'POST',
			['Ajax', 'contact'],
			[
				'name'    => 'amthieu',
				'email'   => 'mathieu@gmail.com',
				'message' => 'message'
			]
		);

		$this->assertContains('true', $output);

	}

}