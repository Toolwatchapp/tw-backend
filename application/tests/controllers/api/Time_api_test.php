<?php

class Time_api_test extends TestCase {


  public function test_time(){

    $output = $this->request(
      'GET', 'api/time',
      []
    );

    $this->assertResponseCode(200);
  }

  public function test_options() {
    $output = $this->request(
      'OPTIONS',
      'api/time'
    );

    $this->assertResponseCode(200);
  }

}
