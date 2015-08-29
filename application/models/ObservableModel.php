<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

class ObservableModel extends MY_Model {

	private $_observers;

	public function __construct() {
		parent::__construct();

		$this->load->model('email');

		$this->_observers = array($this->email, $this->event);

	}

	public function notify($event, $data) {
		foreach ($this->_observers as $observer) {
			$observer->updateObserver($this, $event, $data);
		}
	}

}