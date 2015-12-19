<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

class ObservableModel extends MY_Model {

	private $_observers = array();

	public function __construct() {
		parent::__construct();

		$this->load->model('email');

		if(isset($this->email)){
			$this->_observers[0] = $this->email;
			$this->_observers[1] = $this->event;
		}

	}

	public function notify($event, $data) {

		foreach ($this->_observers as $observer) {
			if($observer !== null){
				$observer->updateObserver($this, $event, $data);
			}
		}
	}
}
