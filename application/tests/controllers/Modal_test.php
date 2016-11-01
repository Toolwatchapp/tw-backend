<?php

class Modal_test extends TestCase {

	public function test_accuracyWarning(){
		$output = $this->request(
			'POST',
			['Modal', 'accuracyWarning'],
			[
				'ajax' => true
			]
		);

		$this->assertContains('<h1>12 hours limit!</h1>', $output);
	}

	public function test_accuracyWarningFail(){
		$output = $this->request('GET', ['Modal', 'accuracyWarning']);
		$this->assertResponseCode(302);
	}

	public function test_login(){
		$output = $this->request(
			'POST',
			['Modal', 'login'],
			[
				'ajax' => true
			]
		);

		$this->assertContains('name="login" method="post"', $output);
	}

	public function test_loginFail(){
		$output = $this->request('GET', ['Modal', 'login']);
		$this->assertResponseCode(302);
	}

	public function test_signUp(){
		$output = $this->request(
			'POST',
			['Modal', 'signUp'],
			[
				'ajax' => true
			]
		);

		$this->assertContains('name="signup" method="post"', $output);
	}

	public function test_signUpFail(){
		$output = $this->request('GET', ['Modal', 'signUp']);
		$this->assertResponseCode(302);
	}

	public function test_resetPassword(){
		$output = $this->request(
			'POST',
			['Modal', 'resetPassword'],
			[
				'ajax' => true
			]
		);

		$this->assertContains('<h1>Reset password</h1>', $output);
	}

	public function test_resetPasswordFail(){
		$output = $this->request('GET', ['Modal', 'resetPassword']);
		$this->assertResponseCode(302);
	}

}
