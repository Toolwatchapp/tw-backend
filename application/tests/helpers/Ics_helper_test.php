<?php

class Ics_helper_test extends TestCase {

  public function setUp() {
		$this->CI = &get_instance();

		$this->CI->load->helper("ics_helper");
	}

  public function test_generateBase64Ics(){
    $this->assertEquals(
     true,
    is_string(generateBase64Ics(1454508967, 1454508967, "Mathieu", "mathieu@gmail.com", "Summary", "iqzd"))
    );
  }

}
?>
