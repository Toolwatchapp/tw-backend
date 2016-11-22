<?php

class EmailPreferences_test extends TestCase {

  private static $userId;

  public static function setUpBeforeClass() {
		$CI = &get_instance();
    $CI->load->model('User');
		$CI->load->model('Watch');
		$CI->load->model('Measure');

		$CI->watch->delete_where(array("watchId >=" => "0"));
		$CI->User->delete_where(array("userId >=" => "0"));
		$CI->Measure->delete_where(array("id >=" => "0"));
  }

  public function setUp() {
    $this->CI = &get_instance();
    $this->CI->load->model('emailpreferences');
    $this->CI->load->library('Session');
    $this->CI->load->model('user');
    $this->obj = $this->CI->emailpreferences;
    $this->user = $this->CI->user;
    $this->session = $this->CI->session;
  }

  public function test_signup(){

      $this->user->signup(
			'mathieu@gmail.com',
			'azerty',
			'math',
			'nay',
			'-5',
			'Canada'
		);

		$this->user->login('mathieu@gmail.com', 'azerty');

    self::$userId = $this->session->userdata('userId');

    $this->assertEquals(1, $this->obj->count_all());

  }

  public function test_defaultValue() {

    $result = $this->obj->getPreferences(self::$userId);

    $this->assertEquals(1, $result->dayAccuracy);
    $this->assertEquals(1, $result->weekAccuracy);
    $this->assertEquals(1, $result->result);
    $this->assertEquals(1, $result->newMeasure);
    $this->assertEquals(1, $result->firstWatch);
    $this->assertEquals(1, $result->firstMeasure);
    $this->assertEquals(1, $result->secondWatch);
    $this->assertEquals(1, $result->comeback);
    $this->assertEquals(self::$userId, $result->userId);
	}

  public function test_changeValue(){
    $res = $this->obj->updateEmailPreferences(0, 0, 1, 1, 0, 0, 0, 0, self::$userId);

    $this->assertEquals(true, $res);

    $result = $this->obj->getPreferences(self::$userId);

    $this->assertEquals(0, $result->dayAccuracy);
    $this->assertEquals(0, $result->weekAccuracy);
    $this->assertEquals(1, $result->result);
    $this->assertEquals(1, $result->newMeasure);
    $this->assertEquals(0, $result->firstWatch);
    $this->assertEquals(0, $result->firstMeasure);
    $this->assertEquals(0, $result->secondWatch);
    $this->assertEquals(0, $result->comeback);
    $this->assertEquals(self::$userId, $result->userId);

  }

  public static function tearDownAfterClass() {
  		$CI = &get_instance();
  		$CI->load->model('User');
  		$CI->load->model('Watch');
  		$CI->load->model('Measure');
  		$CI->watch->delete_where(array("watchId >=" => "0"));
  		$CI->User->delete_where(array("userId >=" => "0"));
  		$CI->Measure->delete_where(array("id >=" => "0"));
  	}

}
