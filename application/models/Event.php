<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

class Event extends MY_Model {

	public $CTA_MEASURES             = "CTA_MEASURES";
	public $CTA_MEASURE_NOW          = "CTA_MEASURE_NOW";
	public $CTA_GET_STARTED          = "CTA_GET_STARTED";
	public $CTA_FEATURES             = "CTA_FEATURES";
	public $LOGIN_EMAIL              = "LOGIN_EMAIL";
	public $LOGIN_FAIL               = "LOGIN_FAIL";
	public $LOGIN_FB                 = "LOGIN_FB";
	public $LOGIN_FB_FAIL            = "LOGIN_FB_FAIL";
	public $RESET_PASSWORD           = "RESET_PASSWORD";
	public $RESET_PASSWORD_USE       = "RESET_PASSWORD_USE";
	public $LOGOUT                   = "LOGOUT";
	public $SIGN_UP                  = "SIGN_UP";
	public $SIGN_UP_FAIL             = "SIGN_UP_FAIL";
	public $SIGN_UP_FB               = "SIGN_UP_FB";
	public $ADD_WATCH                = "ADD_WATCH";
	public $DELETE_WATCH             = "DELETE_WATCH";
	public $NEW_MEASURE              = "NEW_MEASURE";
	public $DELETE_MEASURE           = "DELETE_MEASURE";
	public $DELETE_ALL_MEASURES      = "DELETE_ALL_MEASURES";
	public $BOARD_LOAD               = "BOARD_LOAD";
	public $NEW_ACCURACY             = "NEW_ACCURACY";
	public $MEASURE_LOAD             = "MEASURE_LOAD";
	public $ACCURACY_LOAD            = "ACCURACY_LOAD";
	public $ACCURACY_WARNING_POPUP   = "ACCURACY_WARNING_POPUP";
	public $ACCURACY_SOMETHING_WRONG = "ACCURACY_SOMETHING_WRONG";
	public $LOGIN_POPUP              = "LOGIN_POPUP";
	public $SIGN_UP_POPUP            = "SIGN_UP_POPUP";
	public $HOME_PAGE_0              = "HOME_PAGE_0";
	public $HOME_PAGE_1              = "HOME_PAGE_1";
	public $HOME_PAGE_2              = "HOME_PAGE_2";
	public $HOME_PAGE_3              = "HOME_PAGE_3";

	function __construct() {
		parent::__construct();
	}

	function add($event) {

		if (ENVIRONMENT === 'production') {
			if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
				$_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
			}

			$country = "undefined";

			if (isset($_SERVER["HTTP_CF_IPCOUNTRY"])) {
				$country = $_SERVER["HTTP_CF_IPCOUNTRY"];
			}

			$data = array(
				'ip'      => $_SERVER['REMOTE_ADDR'],
				'user_id' => $this->session->userdata('userId')?
				$this->session->userdata('userId'):0,
				'mobile'   => (int) $this->agent->is_mobile(),
				'browser'  => $this->agent->browser(),
				'platform' => str_replace(' ', '', $this->agent->platform()),
				'country'  => $country,
				'date'     => str_replace(' ', 'T', date("Y-m-d H:i:s")),
				'event'    => $event
			);

			$data_string = json_encode($data);

			event_url();

			$ch = curl_init(event_url());

			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'Content-Length: '.strlen($data_string))
			);

			$result = curl_exec($ch);
		}
	}
}