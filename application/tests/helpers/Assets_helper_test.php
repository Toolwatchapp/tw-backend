<?php

class Assets_helper_test extends TestCase {

  public function setUp() {
		$this->CI = &get_instance();

		$this->CI->load->helper("assets_helper");
	}

  public function test_css(){
    $nom = "plop";
    $this->assertEquals(
      base_url() . 'assets/css/' . $nom . '.css',
      css_url($nom)
    );
  }

  public function test_vid(){
    $nom = "plop";
    $this->assertEquals(
      base_url() . 'assets/videos/' . $nom,
      vid_url($nom)
    );
  }

  public function test_ico(){
    $nom = "plop";
    $this->assertEquals(
      base_url() . 'assets/img/ico/' . $nom,
      ico_url($nom)
    );
  }

  public function test_js(){
    $nom = "plop";
    $this->assertEquals(
      base_url() . 'assets/js/' . $nom . '.js',
      js_url($nom)
    );
  }

  public function test_img(){
    $nom = "plop";
    $this->assertEquals(
      base_url() . 'assets/img/' . $nom,
      img_url($nom)
    );
  }

  public function test_img_tag(){
    $nom = "plop";
    $alt = "iu";
    $class = "ui";

    $this->assertEquals(
      '<img src="' . img_url($nom) . '" alt="' . $alt . '" class="'.$class.'">',
      img($nom, $alt, $class)
    );
  }

  public function test_event(){
    $this->assertEquals(
      getenv("TW_EVENT_URL"),
      event_url()
    );
  }


}
