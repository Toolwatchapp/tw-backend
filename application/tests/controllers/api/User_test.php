<?php

class User_test extends TestCase {

  public static function setUpBeforeClass() {
		$CI = &get_instance();
		$CI->emailWatch   = new MY_Model('email_watch');
		$CI->emailMeasure = new MY_Model('email_measure');
		$CI->emailUser   = new MY_Model('email_user');

		$CI->emailUser->delete_where(array("id >=" => "0"));
		$CI->emailWatch->delete_where(array("id >=" => "0"));
		$CI->emailMeasure->delete_where(array("id >=" => "0"));

		$CI->load->model('User');
		$CI->load->model('Measure');
    $CI->load->model('Watch');
		$CI->load->model('Key');
		$CI->User->delete_where(array("userId >="   => "0"));
		$CI->Measure->delete_where(array("id >="    => "0"));
    $CI->Watch->delete_where(array("watchId >=" => "0"));
		$CI->Key->delete_where(array("id >=" => "0"));
	}

	public function test_create() {
		$output = $this->request(
			'POST',
			['api/user', 'create'],
			[
        'email'       => 'mathieu@gmail.com',
				'password'    => 'password',
				'name'        => 'name',
				'firstname'   => 'firstname',
				'timezone'    => 'timezone',
				'country'     => 'country',
				'mailingList' => 'false'
			]
		);

    var_dump($output);
    
		$this->assertContains('{"success":true}', $output);
	}



}
