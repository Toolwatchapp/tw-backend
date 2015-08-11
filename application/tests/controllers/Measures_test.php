<?php

class Measures_test extends TestCase {

	private static $watchId;
	private static $userId;

	public static function setUpBeforeClass() {
		$CI = &get_instance();
		$CI->load->model('User');
		$CI->load->model('Watch');
		$CI->load->library('Session');
		$CI->load->model('Measure');

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
		$CI->Measure->delete_where(array("id >="    => "0"));
	}

	public function test_index(){
		$output = $this->request('GET', ['Measures', 'index']);
		$this->assertContains('<h1>My measures</h1>', $output);
		$this->assertContains('Add a watch', $output);
	}

	public function test_indexAddWatch(){
		$output = $this->request(
			'POST',
			['Measures', 'index'],
			[
				'addWatch' => true,
				'brand'      => 'brand',
				'name'      => 'name',
				'yearOfBuy'      => 2015,
				'serial'      => 2015,
				'caliber'      => 2015,
			]
		);

		$this->assertContains('Watch successfully added!', $output);
	}

	public function test_indexDeleteMeasures(){
		$output = $this->request(
			'POST',
			['Measures', 'index'],
			[
				'deleteMeasures' => 28
			]
		);

		$this->assertContains('Measures successfully deleted!', $output);
	}

	public function test_indexDeleteWatch(){
		$CI = &get_instance();
		$CI->load->model('Watch');
		$watch = $CI->Watch->find_by('userId', self::$userId);

		$output = $this->request(
			'POST',
			['Measures', 'index'],
			[
				'deleteWatch' => $watch->watchId
			]
		);

		$this->assertContains('Watch successfully deleted!', $output);
	}

	public function test_newWatch(){
		$output = $this->request('GET', ['Measures', 'new_watch']);
		$this->assertContains('<h1>Add a new watch</h1>', $output);
	}

	public function test_newMeasure(){
		$output = $this->request('GET', ['Measures', 'new_measure']);
		$this->assertContains('<h1>New measure</h1>', $output);
	}

	public function test_getAccuracyFail(){
		$output = $this->request('GET', ['Measures', 'get_accuracy']);
		$this->assertResponseCode(302);
	}

	public function test_getAccuracy(){
		$output = $this->request(
			'POST',
			['Measures', 'get_accuracy'],
			[
				'measureId' => 28,
				'watchId' => 28
			]
		);

		$this->assertContains('<h1 id="mainTitle">Check the accuracy</h1>', $output);
	}


}