<?php

class Watches_api_test extends TestCase {

  private static $userKey;
  private static $watchId;

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

  }

  public function test_createUserAndGetAPIKey(){
    $output = $this->request(
			'POST',
			'api/users',
			[
        'email'       => 'mathieu@gmail.com',
				'password'    => 'password',
				'name'        => 'name',
				'firstname'   => 'firstname',
				'timezone'    => 'timezone',
				'country'     => 'country'
			]
		);

    $this->assertContains('"email":"mathieu@gmail.com"', $output);
		$this->assertContains('"key"', $output);
    self::$userKey = json_decode($output)->key;
  }

  public function test_createWatchNoKey(){

    $output = $this->request(
			'POST',
			'api/watches',
			[
        'brand'       => 'brand',
				'name'        => 'name',
				'yearOfBuy'   => 2000,
				'serial'      => 1,
				'caliber'     => 'zdq'
			]
		);

    $this->assertResponseCode(403);
  }

  public function test_createWatchNotAllArgs(){

    $output = $this->request(
      'POST',
      'api/watches',
      [
        'brand'       => 'brand',
        'name'        => 'name',
      ],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertResponseCode(400);
  }
  
  public function test_options() {
    $output = $this->request(
      'OPTIONS',
      'api/watches'
    );

    $this->assertResponseCode(200);
  }

  public function test_createWatch(){

    $output = $this->request(
			'POST',
			'api/watches',
			[
        'brand'       => 'brand',
				'name'        => 'name',
				'yearOfBuy'   => 2000,
				'serial'      => 1,
				'caliber'     => 'zdq'
			],
      null,
      array('X_API_KEY' => self::$userKey)
		);

    $this->assertContains('id', $output);
    self::$watchId = json_decode($output)->id;
  }

  public function test_updateWatchNoKey(){
    $output = $this->request(
			'PUT',
			'api/watches',
			[
        'id'          => self::$watchId,
        'brand'       => 'brand',
				'name'        => 'name',
				'yearOfBuy'   => 2000,
				'serial'      => 1,
				'caliber'     => 'zdq'
			]
		);

    $this->assertResponseCode(403);
  }

  public function test_updateWatchNotAllArgs(){
    $output = $this->request(
      'PUT',
      'api/watches',
      [
        'id'          => self::$watchId,
        'brand'       => 'brand',
      ],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertResponseCode(400);
  }

  public function test_updateWatch(){

    $output = $this->request(
      'PUT',
      'api/watches',
      [
        'id'          => self::$watchId,
        'brand'       => 'branda',
				'name'        => 'name',
				'yearOfBuy'   => 2000,
				'serial'      => 1,
				'caliber'     => 'zdq'
      ],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertContains('true', $output);
  }

  public function test_brandAutocompleteNoKey(){

    $output = $this->request(
      'GET', 'api/watches/brands/j',
      []
    );

    $this->assertResponseCode(403);

  }

  public function test_brandAutocomplete1Letter(){
    $output = $this->request(
      'GET', 'api/watches/brands/j',
      [],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertResponseCode(200);
  }

  public function test_brandAutocomplete(){
    $output = $this->request(
      'GET', 'api/watches/brands/ja',
      [],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertContains('{"name":"Jaeger-LeCoultre","icon":"logo_jaegerlecoultre.jpg","models":"jaegerlecoultre"}', $output);
    $this->assertContains('{"name":"Jaquet Droz","icon":"logo_jaquetdroz.jpg","models":"jaquetdroz"}', $output);
    $this->assertResponseCode(200);
  }

  public function test_modelAutocompleteNoKey(){
    $output = $this->request(
      'GET', 'api/watches/jaegerlecoultre/duo',
      []
    );

    $this->assertResponseCode(403);
  }

  public function test_modelAutocomplete1Letter(){
    $output = $this->request(
      'GET', 'api/watches/models/jaegerlecoultre/d',
      [],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertResponseCode(200);
  }

  public function test_modelAutocompleteBrandDontExist(){
    $output = $this->request(
      'GET', 'api/watches/models/qzdqdqzd/duo',
      [],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertResponseCode(400);
  }

  public function test_modelAutocomplete(){
    $output = $this->request(
      'GET', 'api/watches/models/jaegerlecoultre/duo',
      [],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertContains('Duometre', $output);
    $this->assertResponseCode(200);
  }

  public function testDeleteNoKey(){
    $output = $this->request(
			'DELETE',
			'api/watches',
			[

			]
		);

    $this->assertResponseCode(403);
  }

  public function testDeleteNotAllArgs(){
    $output = $this->request(
			'DELETE',
			'api/watches',
			[],
      null,
      array('X_API_KEY' => self::$userKey)
		);

    $this->assertResponseCode(400);
  }

  public function testDeleteWrongId(){
    $output = $this->request(
      'DELETE',
      'api/watches',
      [
        'watchId' => 0
      ],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertResponseCode(400);
  }

  public function testDelete(){
    $output = $this->request(
      'DELETE',
      'api/watches',
      [
        'watchId' => self::$watchId
      ],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertResponseCode(200);
  }

  public function testLimit(){

    $CI = &get_instance();
    $limits = new MY_MODEL('limits');

    $limits->update(null, ["count"=>601]);


    $output = $this->request(
      'POST',
      'api/watches',
      [
        'brand'       => 'brand',
        'name'        => 'name',
        'yearOfBuy'   => 2000,
        'serial'      => 1,
        'caliber'     => 'zdq'
      ],
      null,
      array('X_API_KEY' => self::$userKey)
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
