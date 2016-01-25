<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

/**
 * ObservableModel extends MY_Model (which handles database transaction)
 * and adds the obervable logic (Observer desgin pattern).
 *
 * The class is abstract, no good reason to create an instance of this.
 * It must be specialized.
 *
 * //TODO: If other classes were to use the observable model. Then,
 * the $_observers and the notify method could be shippied as a trait.
 */
abstract class ObservableModel extends MY_Model {

	private $_observers = array();

	/**
	 * Default constructor
	 */
	public function __construct() {
		parent::__construct();

		$this->load->model('email');

		$this->_observers[0] = $this->email;
		$this->_observers[1] = $this->event;

	}

	/**
	 * Notify method. Notifies the registered observers
	 *
	 * @param  String $event Description of the event
	 * @param  Array $data  Data related to the event
	 */
	public function notify($event, $data) {

		foreach ($this->_observers as $observer) {
			if($observer !== null){
				$observer->updateObserver($this, $event, $data);
			}
		}
	}
}
