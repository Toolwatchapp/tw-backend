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

    $this->assertContains('email taken', $output);
  }

  public function test_loginFacebookOld(){

      //Old facebook
      self::$user->insert(
        array(
          'email'       => 'mathieu_fb@gmail.com',
          //sha256 of '0'
          'password'    => '5feceb66ffc86f38d952786c6d696c79c2dbc239dd4e91b46729d73a27fb57e9',
          'name'    => 'lastname',
          'firstname'        => 'firstname',
          'country'     => 'country',
          'facebook' => 1
        )
      );

      $user = self::$user->find_by('email', 'mathieu_fb@gmail.com');
      $this->assertEquals("5feceb66ffc86f38d952786c6d696c79c2dbc239dd4e91b46729d73a27fb57e9", $user->password);

      $output = $this->request(
        'POST',
        'api/users',
        [
          'email'       => 'mathieu_fb@gmail.com',
          'password'    => 'zsdzsdszd'
        ]
      );

     $this->assertContains('"email":"mathieu_fb@gmail.com"', $output);

     self::$user->insert(
        array(
          'email'       => 'mathieu_fb2@gmail.com',
          'password'    =>  hash('sha256', getenv("FB_PW")."1234"),
          'name'    => 'lastname',
          'firstname'        => 'firstname',
          'country'     => 'country',
          'facebook' => 1
        )
      );

      $user = self::$user->find_by('email', 'mathieu_fb2@gmail.com');
      $this->assertEquals(hash('sha256', getenv("FB_PW")."1234"), $user->password);

       $output = $this->request(
        'POST',
        'api/users',
        [
          'email'       => 'mathieu_fb2@gmail.com',
          'password'    => '1234'
        ]
      );

      $this->assertContains('"email":"mathieu_fb2@gmail.com"', $output);
      $user = self::$user->find_by('email', 'mathieu_fb2@gmail.com');
      $this->assertEquals("5feceb66ffc86f38d952786c6d696c79c2dbc239dd4e91b46729d73a27fb57e9", $user->password);

      Facebook::$is_valid = true;

      $output = $this->request(
        'POST',
        'api/users/facebook',
        [
          'email'       => 'mathieu_fb2@gmail.com',
          'token'    => '1234'
        ]
      );

      Facebook::$is_valid = false;
      $this->assertContains('"email":"mathieu_fb2@gmail.com"', $output);
  }

  public function test_fbApiPost(){
      
      Facebook::$is_valid = true;

      $output = $this->request(
        'POST',
        'api/users/facebook',
        [
          'email'       => 'mathieu_fb2@gmail.com',
          'token'    => '1234'
        ]
      );

      Facebook::$is_valid = false;
      $this->assertContains('"email":"mathieu_fb2@gmail.com"', $output);

      Facebook::$is_valid = true;

      $output = $this->request(
        'POST',
        'api/users/facebook',
        [
          'email'       => 'mathieu_fb3@gmail.com',
          'token'    => '1234'
        ]
      );

      Facebook::$is_valid = false;
      $this->assertContains('"email":"mathieu_fb3@gmail.com"', $output);
  }

  public function test_fbApiFail(){
      

      $output = $this->request(
        'POST',
        'api/users/facebook',
        [
          'email'       => 'mathieu_fb2@gmail.com',
          'token'    => '1234'
        ]
      );

      $this->assertResponseCode(401);
  }

  public function test_fbAPIBadRequest(){
      

      $output = $this->request(
        'POST',
        'api/users/facebook',
        [
          'email'       => 'mathieu_fb2@gmail.com'
        ]
      );

      $this->assertResponseCode(400);
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

    $user = self::$user->find_by('email', 'mathieu@gmail.com');

    $this->assertContains('"email":"mathieu@gmail.com"', $output);
		$this->assertContains('"key"', $output);
    self::$userKey = json_decode($output)->key;
  }

  public function test_oldFacebook(){
    $output = $this->request(
        'POST',
        'api/users',
        [
          'email'       => 'mathieu_fb@gmail.com',
				  'password'    => getenv("FB_PW").'random_fb_id'
        ]
    );

    $this->assertContains('"email":"mathieu_fb@gmail.com"', $output);
		$this->assertContains('"key"', $output);
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

  public function test_password(){
      $output = $this->request(
        'POST',
        'api/users/reset',
        [
          'email'       => 'mathieu_fb2@gmail.com',
        ]
      );

      $this->assertResponseCode(200);
  }

  public function test_passwordFail(){
      $output = $this->request(
        'POST',
        'api/users/reset',
        [
          'email'       => 'fsefsefsfe@gmail.com',
        ]
      );

      $this->assertResponseCode(400);

      $output = $this->request(
        'POST',
        'api/users/reset',
        [
          'ssdawd'       => 'fsefsefsfe@gmail.com',
        ]
      );

      $this->assertResponseCode(400);
  }

  public function testLimit(){

    $this->test_create();

   
    for ($i = 1; $i <= 2; $i++) {


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

    $output = $this->request(
			'POST',
			'api/users/reset',
			[
        'email'       => 'mathieu2@gmail.com'
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
