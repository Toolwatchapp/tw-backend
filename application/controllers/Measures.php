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
 * - Compute the base measures (1/2, baseMeasure) and accuracy measures
 *  (2/2, accuracyMeasure)
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

		$this->event->add(BOARD_LOAD);
		$this->constructMeasurePage();
	}

	/**
	 * Construct the views of the measure page (board).
	 *
	 * @return mixed|html Board view
	 */
	private function constructMeasurePage(){

		$this->_headerData['headerClass'] = 'blue';
		if (!$this->agent->is_mobile()) {
			array_push($this->_headerData['javaScripts'], "watch.animation", "time");
		}

		$this->load->view('header', $this->_headerData);

		$this->_bodyData['allMeasure'] = $this->measure->getMeasuresByUser(
			$this->session->userdata('userId'));

		$this->load->view('measure/dashboard', $this->_bodyData);

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
						$this->brand, $this->name,
						$this->yearOfBuy, $this->serial,
						$this->caliber)) {

				$this->_bodyData['success'] = 'Watch successfully added!';

			} else {
				$this->_bodyData['error'] = 'An error occured while adding your watch.';
			}

			$this->constructMeasurePage();
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

		if($this->expectsPost(array('watchId'))){

			if ($this->watch->deleteWatch($this->watchId)) {
				$this->_bodyData['success'] = 'Watch successfully deleted!';
			} else {
				$this->_bodyData['error'] = 'An error occured while deleting your watch.';
		  }

			$this->constructMeasurePage();
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

			if ($this->measure->deleteMesure($this->measureId)) {
				$this->_bodyData['success'] = 'Measures successfully deleted!';
			} else {
				$this->_bodyData['error'] = 'An error occured while deleting your measures.';
			}

			$this->constructMeasurePage();
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

		$this->_bodyData['watches'] = $this->watch->getWatches(
			$this->session->userdata('userId'));

		$this->event->add(MEASURE_LOAD);

		$this->_headerData['headerClass'] = 'blue';
		$this->load->view('header', $this->_headerData);

		$this->load->view('measure/new-measure', $this->_bodyData);
		$this->load->view('measure/audio.php');

		$this->load->view('footer');
	}

	/**
	 * Serves the new measure form (1/2) for a watch
	 * with existing measures.
	 */
	public function new_measure_for_watch(){

		if($this->expectsPost(array('watchId'))){

			$this->_bodyData['selected_watch'] = $this->watchId;

			$this->new_measure();

		}
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

	/**
	 * getReferenceTime. Set the server time to the user session.
	 *
	 * This must to be call by the user before accuracyMeasure or baseMeasure
	 */
	function getReferenceTime() {
		$this->session->set_userdata('referenceTime', time());
	}

	/**
	 * Save the base measure (1/2)
	 *
	 * TODO: When this becomes doable from many endpoints (web, mobile, ...)
	 * I'll to move it to the model.
	 *
	 * @param POST String watchId
	 * @param POST String userTimezone
	 * @param POST String userTime
	 *
	 * @return boolean JSON
	 */
	function baseMeasure() {

		if ($this->expectsPost(array('watchId', 'userTimezone', 'userTime'))) {

			$result = array();

			$watchId       = $this->input->post('watchId');

			//Construct user time
			$referenceTime = $this->session->userdata('referenceTime');
			$userTimezone  = $this->input->post('userTimezone');
			$tempUserTime	 = preg_split('/:/', $this->input->post('userTime'));
			$userTime 		 = mktime($tempUserTime[0], $tempUserTime[1],
			 	$tempUserTime[2], date("n"), date("j"), date("Y"));

			//Add the base measure
			if ($this->measure->addBaseMesure($watchId, $referenceTime, $userTime)) {

				$result['success'] = true;

			} else {
				$result['success'] = false;
			}

			echo json_encode($result);
		}
	}


	/**
	 * Save the accuracy measure (2/2).
	 *
	 * TODO: When this becomes doable from many endpoints (web, mobile, ...)
	 * I'll to move it to the model.
	 *
	 * FIXME: userTimezone parameter isn't used. Should it ?
	 *
	 * @param POST String measureId
	 * @param POST String userTimezone
	 * @param POST String userTime
	 *
	 * @return Array['accuracy', 'success'] JSON
	 */
	function accuracyMeasure() {

		if ($this->expectsPost(array('measureId', 'userTimezone', 'userTime'))) {

			//Construct the user time
			$referenceTime = $this->session->userdata('referenceTime');
			$userTimezone  = $this->input->post('userTimezone');
			$tempUserTime = preg_split('/:/', $this->input->post('userTime'));
			$userTime = mktime($tempUserTime[0], $tempUserTime[1], $tempUserTime[2],
				date("n"), date("j"), date("Y"));

			//Add the watch measure
			$watchMeasure = $this->measure->addAccuracyMesure(
				$this->input->post('measureId'), $referenceTime, $userTime);

			//We store the computed accuracy
			$result['accuracy'] = $watchMeasure->accuracy;

			// If the computed accuracy makes sense, we return success
			if (is_numeric($watchMeasure->accuracy)) {
				$result['success'] = true;
			} else {
				$result['success'] = false;
			}

			echo json_encode($result);
		}
	}
}
