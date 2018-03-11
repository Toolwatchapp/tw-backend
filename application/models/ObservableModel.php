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

	private static $_observers = null;
	/**
	 * Default constructor
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Notify method. Notifies the registered observers
	 *
	 * @param  String $event Description of the event
	 * @param  Array $data  Data related to the event
	 */
	public function notify($event, $data) {

		if(self::$_observers === null){
			self::$_observers = array(new Auto_email(), new Event());
		}

		log_message('info', 'Notify: ' . $event . " : " . print_r($data, true));

		foreach (self::$_observers as $observer) {
				$observer->updateObserver($this, $event, $data);
		}
	}
}
