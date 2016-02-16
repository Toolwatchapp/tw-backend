<?php

class Hooks_test extends TestCase {

	private static $userId;

	public static function setUpBeforeClass() {
		$CI = &get_instance();
		$CI->load->model('User');
		$CI->load->model('Watch');
		$CI->load->library('Session');
		$CI->load->model('Measure');

		$CI->Watch->delete_where(array("watchId >=" => "0"));
		$CI->Measure->delete_where(array("id >="    => "0"));
		$CI->User->delete_where(array("userId >="    => "0"));

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

		$watchId = $CI->Watch->addWatch(
			self::$userId,
			'brand',
			'name',
			2015,
			28,
			014
		);

		$watchId = $CI->Watch->addWatch(
			self::$userId,
			'branda',
			'nama',
			2015,
			28,
			014
		);

		$CI->Measure->addBaseMesure(
			$watchId,
			time(),
			time()
		);

		$watchId = $CI->Watch->addWatch(
			self::$userId,
			'branda',
			'nama',
			2015,
			28,
			014
		);

		$CI->Measure->addBaseMesure(
			$watchId,
			time()-12*60*60,
			time()-12*60*60
		);

		$watchId = $CI->Watch->addWatch(
			self::$userId,
			'branda',
			'nama',
			2015,
			28,
			014
		);

		$measureId = $CI->Measure->addBaseMesure(
			$watchId,
			time()-12*60*60,
			time()-12*60*60
		);

		$CI->Measure->addAccuracyMesure(
			$measureId,
			time()-12*60*60,
			time()-12*61*61
		);


	}

	public function test_index() {
		$output = $this->request(
			'POST',
			['Hooks', 'index'],
			[
				'token' => 'bPiAi9XNEa3p9FF1lQnZfuUY',
				'text'  => 'Jack nbusers'
			]
		);

		$this->assertContains('1. ', $output);
	}

	public function test_indexMeasures() {
		$output = $this->request(
			'POST',
			['Hooks', 'index'],
			[
				'token' => 'bPiAi9XNEa3p9FF1lQnZfuUY',
				'text'  => 'Jack nbmeasures'
			]
		);

		$this->assertContains('3. ', $output);
	}

	public function test_indexWatch() {
		$output = $this->request(
			'POST',
			['Hooks', 'index'],
			[
				'token' => 'bPiAi9XNEa3p9FF1lQnZfuUY',
				'text'  => 'Jack nbwatches'
			]
		);

		$this->assertContains('4. ', $output);
	}

	public function test_indexWhois() {
		$output = $this->request(
			'POST',
			['Hooks', 'index'],
			[
				'token' => 'bPiAi9XNEa3p9FF1lQnZfuUY',
				'text'  => 'Jack whois mathieu@gmail.com'
			]
		);

		$this->assertContains('ID', $output);
	}

	public function test_indexWhoisSlackVersion() {
		$output = $this->request(
			'POST',
			['Hooks', 'index'],
			[
				'token' => 'bPiAi9XNEa3p9FF1lQnZfuUY',
				'text'  => 'Jack whois <mailto:mathieu@gmail.com|mathieu@gmail.com>'
			]
		);

		$this->assertContains('ID', $output);
	}

	public function test_indexWhoisFail(){
		$output = $this->request(
			'POST',
			['Hooks', 'index'],
			[
				'token' => 'bPiAi9XNEa3p9FF1lQnZfuUY',
				'text'  => 'Jack whois qzdqzd@gmail.com'
			]
		);

		$this->assertContains('User not found. ', $output);
	}

	public function test_indexHelp() {
		$output = $this->request(
			'POST',
			['Hooks', 'index'],
			[
				'token' => 'bPiAi9XNEa3p9FF1lQnZfuUY',
				'text'  => 'Jack help'
			]
		);

		$this->assertContains('`Jack nbusers` ; `Jack nbmeasures` ; `Jack nbwatches`; `Jack whois email`.', $output);
	}

	public function test_Email(){
		$output = $this->request(
			'GET',
			['Hooks', 'email', 'bPiAi9XNEa3p9FF1lQnZfuUY']
		);

		$this->assertContains("Emails sent", $output);
	}

	public function test_resetEmail(){
		$output = $this->request(
			'GET',
			['Hooks', 'reset_email', 'bPiAi9XNEa3p9FF1lQnZfuUY']
		);

		$this->assertContains("Reset success", $output);
	}

}
