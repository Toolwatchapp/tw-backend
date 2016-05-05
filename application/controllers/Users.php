<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

/**
 * Ajax controller.
 *
 * This controller is in charge of every ajax call.
 *
 * TODO: Having an Ajax controller doesn't make much sense to me.
 * In my opignion, methods containes here should be distributed in
 * related controller and properly documented as method expecting
 * Ajax behaviour.
 */
class Users extends MY_Controller {

  public function __construct() {
		$this->_needLoggedIn = true;
		parent::__construct();
		$this->load->model('watch');
    $this->load->model('measure');
		$this->load->model('user');
		$this->load->library('auto_email');
	}



}
