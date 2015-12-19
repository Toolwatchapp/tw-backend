<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

/**
 * Measures controller
 *
 * In charge of
 *
 * - Loading the board (index)
 * - Deleting watch and measures (softly, delete_watch, delete_measure)
 * - Serve add watch from (new_watch) and to add watches (add_watch)
 * - Serve the base measure (1/2) page (new_measure) and the accuracy page
 * (2/2).
 *
 * TODO: Adding measure is still the responsability of the Ajax controller.
 */
class Measures extends MY_Controller {


	public function __construct() {
		$this->_needLoggedIn = true;
		parent::__construct();
		$this->load->model('watch');
		$this->load->model('measure');
	}

	/**
	 * Loads the dashboard of an user.
	 */
	public function index() {

		$this->event->add($this->event->BOARD_LOAD);

		$this->_headerData['headerClass'] = 'blue';
		$this->load->view('header', $this->_headerData);

		$this->_bodyData['watches']    = $this->watch->getWatches(
			$this->session->userdata('userId'));
		$this->_bodyData['allMeasure'] = $this->measure->getMeasuresByUser(
			$this->session->userdata('userId'), $this->_bodyData['watches']);

		$this->load->view('measure/all', $this->_bodyData);

		$this->load->view('footer');
	}

	/**
	 * Add a new watch for an user.
	 *
	 * Update the bodyData variable with the status of the insert
	 *
	 * @param POST String brand
	 * @param POST String name
	 * @param POST String yearOfBuy
	 * @param POST String serial
	 * @param POST String caliber
	 */
	public function add_watch(){
		if($this->expectsPost(array('brand', 'name', 'yearOfBuy',
			'serial', 'caliber'))){

			if ($this->watch->addWatch($this->session->userdata('userId'),
						$brand, $name, $yearOfBuy, $serial, $caliber)) {

				$this->_bodyData['success'] = 'Watch successfully added!';

			} else {
				$this->_bodyData['error'] = 'An error occured while adding your watch.';
			}
		}
	}

	/**
	 * Delete a watch for an user
	 *
	 * It is noteworthy that the watch model will do a soft delete here.
	 *
	 * Update the bodyData variable with the status of the delete.
	 *
	 * @param POST String $deleteWatch The id of the watch to delete.
	 */
	public function delete_watch(){

		if($this->expectsPost(array('deleteWatch'))){
			$watchId = $this->input->post('deleteWatch');

			if ($this->watch->deleteWatch($watchId)) {
				$this->_bodyData['success'] = 'Watch successfully deleted!';
			} else {
				$this->_bodyData['error'] = 'An error occured while deleting your watch.';
		  }
		}
	}

	/**
	 * Deletes a measure for an user.
	 *
	 * It is noteworthy that the measure model will do a soft delete here.
	 *
	 * Update the bodyData variable with the status of the delete.
	 *
	 * @param POST String $deleteMeasures The id of the measure to delete
	 */
	public function delete_measure(){

		if($this->expectsPost(array('deleteMeasures'))){
			$measureId = $this->input->post('deleteMeasures');

			if ($this->measure->deleteMesure($measureId)) {
				$this->_bodyData['success'] = 'Measures successfully deleted!';
			} else {
				$this->_bodyData['error'] = 'An error occured while deleting your measures.';
			}
		}
	}

	/**
	 * Serves the new watch form
	 */
	public function new_watch() {
		$this->event->add(ADD_WATCH_LOAD);

		$this->_headerData['headerClass'] = 'blue';
		$this->load->view('header', $this->_headerData);

		$this->load->view('measure/new-watch', $this->_bodyData);

		$this->load->view('footer');
	}

	/**
	 * Serves the new measure form (1/2)
	 */
	public function new_measure() {

		$this->event->add(MEASURE_LOAD);

		$this->_headerData['headerClass'] = 'blue';
		$this->load->view('header', $this->_headerData);

		$this->_bodyData['watches'] = $this->watch->getWatches($this->session->userdata('userId'));
		$this->load->view('measure/new-measure', $this->_bodyData);
		$this->load->view('measure/audio.php');

		$this->load->view('footer');
	}

	/**
	 * Serves the new accuracy form (2/2)
	 */
	public function get_accuracy() {

		if($this->expectsPost(array('measureId', 'watchId'))){

			$this->event->add(ACCURACY_LOAD);

			$this->_headerData['headerClass'] = 'blue';
			array_push($this->_headerData['javaScripts'], "jquery.sharrre.min", "sharrre.logic", "watch.animation");
			$this->load->view('header', $this->_headerData);

			$this->_bodyData['selectedWatch'] = $this->watch->getWatch($this->input->post('watchId'));
			$this->_bodyData['measureId']     = $this->input->post('measureId');

			$this->load->view('measure/get-accuracy', $this->_bodyData);
			$this->load->view('measure/audio.php');

			$this->load->view('footer');

		} else {
			redirect('/measures/');
		}
	}
}
