<?php

class Status_api_test extends TestCase {

  public function test_brandAutocomplete1Letter(){
    $output = $this->request(
      'GET', 'api/status',
      [],
      null
    );

    $this->assertResponseCode(200);
  }

}
