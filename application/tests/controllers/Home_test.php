<?php

class Home_test extends TestCase {

	public function test_index() {
		$output = $this->request('GET', ['Home', 'index']);
		$this->assertContains('class="home-intro"', $output);
		$this->assertContains('class="home-picto"', $output);
		$this->assertContains('id="demo-screen"', $output);
		$this->assertContains('id="mosa-screen"', $output);
		$this->assertContains('<footer>', $output);
	}

	public function test_result() {
		$output = $this->request('GET', ['Home', 'result']);
		$this->assertContains('accuracy.jpg', $output);
	}

	public function test_about() {
		$output = $this->request('GET', ['Home', 'about']);
		$this->assertContains('<title>Toolwatch • About Toolwatch</title>', $output);
	}

	public function test_contact() {
		$output = $this->request('GET', ['Home', 'contact']);
		$this->assertContains('<title>Toolwatch • Contact</title>', $output);
		$this->assertContains('name="contact" class="form-horizontal"', $output);
	}

	public function test_resetPassword() {
		$output = $this->request('GET', ['Home', 'resetPassword']);
		$this->assertContains('<div class="col-md-12"><center><h1>Reset your password</h1></center></div>', $output);
	}

	public function test_logout_fail(){
		$output = $this->request('GET', ['Home', 'logout']);
		$this->assertContains('false', $output);
	}

	public function test_logout_success(){
		$output = $this->request(
			'POST',
			['Home', 'logout'],
			[
			]

		);
		$this->assertContains('true', $output);
	}

}
