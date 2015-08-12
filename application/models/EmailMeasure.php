<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

class EmailMeasure extends MY_Model {

	function __construct() {
		parent::__construct();
		$this->table_name = "email_measure";
	}

}