<?php

class Array_helper_test extends TestCase {

  public function setUp() {
		$this->CI = &get_instance();

		$this->CI->load->helper("array_helper");
	}

  public function test_arrayToObject() {

    $this->assertEquals(
      "test",
      arrayToObject(array("test"=>"test"))->test);

	}

  public function test_arrayToObjectEmbedded(){
    $this->assertEquals(
      "value",
      arrayToObject(array("test"=>array("emb"=>"value")))->test->emb);
  }

}
