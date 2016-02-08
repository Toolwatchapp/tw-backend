<?php

class Key_test extends TestCase {

  private static $userId;

	public static function setUpBeforeClass() {
		$CI = &get_instance();
		$CI->load->model('User');
    $CI->load->model('Watch');
		$CI->load->model('Key');
		$CI->load->library('Session');

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

		$CI->Key->delete_where(array("key >=" => "0"));
	}

  public function test_generateKey(){

    $user = new stdClass();
    $user->userId = self::$userId;

    $key = new Key();

    $generatedKey = $key->generate_key($user);

    $this->assertEquals(true, is_string($generatedKey));

  }


}
?>
