<?php

class Users_api_test extends TestCase {

  private static $userKey;
  private static $user;

  public static function setUpBeforeClass() {
		$CI = &get_instance();

		$CI->load->model('User');
		$CI->load->model('Measure');
    $CI->load->model('Watch');
		$CI->load->model('Key');
    $CI->ip_throttle = new MY_Model("limits_ip", 'ip');
		$CI->Key->delete_where(array("id >=" => "0"));
		$CI->ip_throttle->delete_where(array("hour_started >=" => "0"));
		$CI->User->delete_where(array("userId >="   => "0"));
		$CI->Measure->delete_where(array("id >="    => "0"));
    $CI->Watch->delete_where(array("watchId >=" => "0"));

    self::$user = $CI->User;
	}

	public function test_create() {
		$output = $this->request(
			'POST',
			'api/users',
			[
        'email'       => 'mathieu@gmail.com',
				'password'    => 'password',
				'lastname'    => 'lastname',
				'name'        => 'firstname',
				'country'     => 'country'
			]
		);

    $this->assertContains('"email":"mathieu@gmail.com"', $output);
		$this->assertContains('"key"', $output);
	}

  public function test_options() {
    $output = $this->request(
      'OPTIONS',
      'api/users'
    );

    $this->assertResponseCode(200);
  }

  public function test_createReject(){
    $output = $this->request(
			'POST',
			'api/users',
			[
        'email'       => 'mathieu@gmail.com',
				'password'    => 'passwordzszcsz',
				'lastname'    => 'lastname',
				'name'        => 'firstname',
				'country'     => 'country'
			]
    );
    $this->assertContains('email taken', $output);
    $this->assertResponseCode(401);
  }

  public function test_createGetLoggedAccountExists(){
    	$output = $this->request(
			'POST',
			'api/users',
			[
        'email'       => 'mathieu@gmail.com',
				'password'    => 'password',
				'lastname'    => 'lastname',
				'name'        => 'firstname',
				'country'     => 'country'
			]
		);

    $this->assertContains('"email":"mathieu@gmail.com"', $output);
		$this->assertContains('"key"', $output);
  }

  public function test_loginFacebook(){

      //Old facebook
      self::$user->insert(
        array(
          'email'       => 'mathieu_fb@gmail.com',
          //sha256 of '0'
          'password'    => '5feceb66ffc86f38d952786c6d696c79c2dbc239dd4e91b46729d73a27fb57e9',
          'name'    => 'lastname',
          'firstname'        => 'firstname',
          'country'     => 'country'
        )
      );

      $user = self::$user->find_by('email', 'mathieu_fb@gmail.com');
      $this->assertEquals("5feceb66ffc86f38d952786c6d696c79c2dbc239dd4e91b46729d73a27fb57e9", $user->password);

      $output = $this->request(
        'PUT',
        'api/users',
        [
          'email'       => 'mathieu_fb@gmail.com',
          'password'    => '0'
        ]
      );

      $this->assertResponseCode(400, "Can't login  0");

    	$output = $this->request(
        'POST',
        'api/users',
        [
          'email'       => 'mathieu_fb@gmail.com',
          'password'    => '0',
          'lastname'    => 'lastname',
          'name'        => 'firstname',
          'country'     => 'country'
        ]
      );

     $this->assertResponseCode(400, "Can't create an account with 0");

     $output = $this->request(
        'POST',
        'api/users/facebook',
        [
          'email'       => 'mathieu_fb@gmail.com',
          'password'    => 'random_fb_id',
          'lastname'    => 'lastname',
          'name'        => 'firstname',
          'country'     => 'country'
        ]
      );

     $this->assertContains('"email":"mathieu_fb@gmail.com"', $output);
		 $this->assertContains('"key"', $output, "successful deprecated facebook account login");

     $user = self::$user->find_by('email', 'mathieu_fb@gmail.com');

     $this->assertEquals(
      $user->password, 
      hash('sha256', getenv("FB_PW").'random_fb_id'),
      "Facebook password should be updated"
     );

     $this->assertEquals($user->facebook, 1,"Facebook tag should be one");

     $output = $this->request(
        'POST',
        'api/users/facebook',
        [
          'email'       => 'mathieu_fb@gmail.com',
          'password'    => 'random_fb_id',
          'lastname'    => 'lastname',
          'name'        => 'firstname',
          'country'     => 'country'
        ]
      );

     $this->assertContains('"email":"mathieu_fb@gmail.com"', $output);
		 $this->assertContains('"key"', $output, "successful updated facebook account login");

     $output = $this->request(
        'POST',
        'api/users/facebook',
        [
          'email'       => 'mathieu_fb_2@gmail.com',
          'password'    => 'random_fb_id',
          'lastname'    => 'lastname',
          'name'        => 'firstname',
          'country'     => 'country'
        ]
      );

     $this->assertContains('"email":"mathieu_fb_2@gmail.com"', $output);
		 $this->assertContains('"key"', $output);

    $output = $this->request(
        'POST',
        'api/users/facebook',
        [
          'email'       => 'mathieu_fb_2@gmail.com',
          'password'    => 'random_fb_id2',
          'lastname'    => 'lastname',
          'name'        => 'firstname',
          'country'     => 'country'
        ]
      );

    $this->assertContains('email taken', $output);
    $this->assertResponseCode(401);

  }

  public function test_login(){

    $output = $this->request(
			'PUT',
			'api/users',
			[
        'email'       => 'mathieu@gmail.com',
				'password'    => 'password'
			]
    );

    $this->assertContains('"email":"mathieu@gmail.com"', $output);
		$this->assertContains('"key"', $output);
    self::$userKey = json_decode($output)->key;
  }

  public function testLoginFail(){
    $output = $this->request(
			'PUT',
			'api/users',
			[
        'email'       => 'mathieu@gmail.com',
				'password'    => 'paqzdqzdssword'
			]
    );

    $this->assertResponseCode(401);
  }

  public function testLoginBadRequest(){
    $output = $this->request(
			'PUT',
			'api/users',
			[
				'password'    => 'paqzdqzdssword'
			]
    );

    $this->assertResponseCode(400);
  }

  public function testGetNoKeyBadRequest(){
    $output = $this->request(
      'GET',
      'api/users',
      [
      ]
    );

    $this->assertResponseCode(403);
  }

  public function testGetWrongKey(){
    $output = $this->request(
      'GET',
      'api/users',
      [],
      null,
      array('X_API_KEY' => "ajkwhdawjikhdajkdn")
    );

    $this->assertResponseCode(403);
  }

  public function testGet(){
    $output = $this->request(
      'GET',
      'api/users',
      [],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertResponseCode(200);
    log_message('info', $output);
    $this->assertContains('"email":"mathieu@gmail.com"', $output);
    $this->assertContains('"watches"', $output);
  }


  public function testDeleteFailNoKey(){
    $output = $this->request(
			'DELETE',
			'api/users',
			[
        'id'       => 200
			]
    );

    $this->assertResponseCode(403);
  }

  public function testDeleteFailBadKey(){

    $this->request(
      'DELETE',
      'api/users',
      [],
      null,
      [
        'X_API_KEY'=> "some key"
      ]
    );

    $this->assertResponseCode(403);
  }

  public function testDelete(){

    $this->request(
      'DELETE',
      'api/users',
      [],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertResponseCode(204);
  }

  public function testLimit(){

    $this->test_create();

   
    for ($i = 1; $i <= 7; $i++) {


      $this->test_login();

    }
    $output = $this->request(
			'PUT',
			'api/users',
			[
        'email'       => 'mathieu@gmail.com',
				'password'    => 'password'
			]
    );

    $this->assertResponseCode(401);

    $output = $this->request(
			'POST',
			'api/users',
			[
        'email'       => 'mathieu2@gmail.com',
				'password'    => 'password',
				'lastname'    => 'lastname',
				'name'        => 'firstname',
				'country'     => 'country'
			]
		);

    $this->assertResponseCode(401);

    $output = $this->request(
			'POST',
			'api/users/facebook',
			[
        'email'       => 'mathieu2@gmail.com',
				'password'    => 'password',
				'lastname'    => 'lastname',
				'name'        => 'firstname',
				'country'     => 'country'
			]
		);

    $this->assertResponseCode(401);

  }

  public static function tearDownAfterClass() {
   $CI = &get_instance();
		$CI->load->model('User');
		$CI->load->model('Measure');
    $CI->load->model('Watch');
		$CI->load->model('Key');
    $CI->ip_throttle = new MY_Model("limits_ip", 'ip');
		$CI->Key->delete_where(array("id >=" => "0"));
		$CI->ip_throttle->delete_where(array("hour_started >=" => "0"));
		$CI->User->delete_where(array("userId >="   => "0"));
		$CI->Measure->delete_where(array("id >="    => "0"));
    $CI->Watch->delete_where(array("watchId >=" => "0"));
  }
}
