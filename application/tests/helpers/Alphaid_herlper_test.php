<?php

class Alphaid_helper_test extends TestCase {

  public static $stringId;

  public function setUp() {
		$this->CI = &get_instance();

		$this->CI->load->helper("alphaid_helper");
	}

  public function test_intToString() {

    self::$stringId = alphaID(1);

    $this->assertEquals(is_string(self::$stringId), true);
  }

  public function test_stringToInt(){
      $this->assertEquals(1, alphaID(self::$stringId, true));
  }

}
