<?php

class Measures_api_test extends TestCase {

  private static $userKey;
  private static $watchId;
  private static $measureId;

  public static function setUpBeforeClass() {

    $CI = &get_instance();
    $CI->load->model('User');
    $CI->load->model('Measure');
    $CI->load->model('Watch');
    $CI->load->model('Key');
    $CI->Key->delete_where(array("id >=" => "0"));
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

    $this->assertContains('"brand":"brand"', $output);
    self::$watchId = json_decode($output)[0]->watchId;
  }

  public function test_addMeasureNoKey(){

    $output = $this->request(
      'POST',
      'api/measures',
      [  ]
    );

    $this->assertResponseCode(403);
  }

  public function test_addMeasureNotAllArgs(){
    $output = $this->request(
      'POST',
      'api/measures',
      [
        'referenceTime' => microtime(),
        'userTime' => microtime()
      ],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertResponseCode(400);
  }

  public function test_addMeasureNotOwnerOfWatch(){
    $output = $this->request(
      'POST',
      'api/measures',
      [
        'watchId' => 0,
        'referenceTime' => round(microtime(true) * 1000),
        'userTime' => round(microtime(true) * 1000)
      ],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertResponseCode(400);
  }

  public function test_addMeasure(){
    $output = $this->request(
      'POST',
      'api/measures',
      [
        'watchId' => self::$watchId,
        'referenceTime' => round(microtime(true) * 1000),
        'userTime' => round(microtime(true) * 1000)
      ],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertResponseCode(200);
    $parsedOutput = json_decode($output);
    $this->assertEquals(true, is_numeric($parsedOutput->measureId));
    self::$measureId = $parsedOutput->measureId;
  }

  public function test_addAccuracyMeasureNoKey(){

    $output = $this->request(
      'PUT',
      'api/measures',
      [  ]
    );

    $this->assertResponseCode(403);
  }

  public function test_addAcuraccyMeasureNotAllArgs(){
    $output = $this->request(
      'PUT',
      'api/measures',
      [
        'referenceTime' => microtime(),
        'userTime' => microtime()
      ],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertResponseCode(400);
  }

  public function test_addAcuracyMeasureNotOwnerOfMeasure(){
    $output = $this->request(
      'PUT',
      'api/measures',
      [
        'measureId' => 0,
        'referenceTime' => round(microtime(true) * 1000),
        'userTime' => round(microtime(true) * 1000)
      ],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertResponseCode(400);
  }

  public function test_addAcuracyMeasure(){
    $output = $this->request(
      'PUT',
      'api/measures',
      [
        'measureId' => self::$measureId,
        'referenceTime' => round(microtime(true) * 1000)+1000,
        'userTime' => round(microtime(true) * 1000)+1000
      ],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertResponseCode(200);
    $parsedOutput = json_decode($output);
    $this->assertEquals(true, is_numeric($parsedOutput->accuracy));
  }

  public function test_deleteNoKey(){

    $output = $this->request(
      'DELETE',
      'api/measures',
      [  ]
    );

    $this->assertResponseCode(403);
  }

  public function test_deleteNotAllArgs(){
    $output = $this->request(
      'DELETE',
      'api/measures',
      [
      ],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertResponseCode(400);
  }

  public function test_deleteMeasureNotOwnerOfMeasure(){
    $output = $this->request(
      'DELETE',
      'api/measures',
      [
        'measureId' => 0,
      ],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertResponseCode(400);
  }

  public function test_deleteMeasure(){
    $output = $this->request(
      'DELETE',
      'api/measures',
      [
        'measureId' => self::$measureId
      ],
      null,
      array('X_API_KEY' => self::$userKey)
    );

    $this->assertResponseCode(200);
    $this->assertContains("true", $output);
  }

  public function testLimit(){

    $CI = &get_instance();
    $limits = new MY_MODEL('limits');

    $limits->update(null, ["count"=>601]);


    $output = $this->request(
      'POST',
      'api/measures',
      [
        'watchId' => self::$watchId,
        'referenceTime' => round(microtime(true) * 1000),
        'userTime' => round(microtime(true) * 1000)
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
   $CI->Key->delete_where(array("id >=" => "0"));
   $CI->User->delete_where(array("userId >="   => "0"));
   $CI->Measure->delete_where(array("id >="    => "0"));
   $CI->Watch->delete_where(array("watchId >=" => "0"));
  }


}
