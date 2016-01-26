<?php

class Modal_test extends TestCase {

	public function test_ctaClickFail(){
		$output = $this->request('GET', ['Modal', 'ctaClick']);
		$this->assertResponseCode(200);
	}


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

		$this->assertContains('<form method="post" name="login">', $output);
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

		$this->assertContains('<form method="post" name="signup">', $output);
	}

	public function test_signUpFail(){
		$output = $this->request('GET', ['Modal', 'signUp']);
		$this->assertResponseCode(302);
	}


	public function test_signUpSuccess(){
		$output = $this->request(
			'POST',
			['Modal', 'signUpSuccess'],
			[
				'ajax' => true
			]
		);

		$this->assertContains('<h1>Well done & Thank you!</h1>', $output);
	}

	public function test_signUpSuccessFail(){
		$output = $this->request('GET', ['Modal', 'signUpSuccess']);
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
