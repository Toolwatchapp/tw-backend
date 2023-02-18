<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

/**
 * Specialisation of CI_Controller that provided some additional services
 * - headerdata
 * - login gestion
 * - post data check
 * - logging
 */
class MY_Controller extends CI_Controller {
	protected $_headerData   = array();
	protected $_bodyData     = array();
	protected $_footerData   = array();
	protected $_needLoggedIn = false;

	/**
	 * Default constructor
	 */
	public function __construct() {
		parent::__construct();

		$this->_headerData['userIsLoggedIn'] = $this->user->isLoggedIn();
		$this->_headerData['styleSheets']    = array('main');
		$this->_headerData['javaScripts']    = array('jquery.min', 'bootstrap.min', 'application', 'MediaElement/mediaelement-and-player.min',"js.cookie");
		$this->_headerData['headerClass'] = '';

		 // include manually module library - SendInBlue API
		require_once (APPPATH . '../vendor/autoload.php');

		\Sentry\init(['dsn' => 'https://80282b24dff94e1cb70f7b477951be25@o4504401871241216.ingest.sentry.io/4504684074893312' ]);

		if ($this->_needLoggedIn && !$this->user->isLoggedIn()) {
			redirect(base_url());
		}
	}

	/**
	 * Convenient method to check that variables have been posted.
	 * In addition, all the variables contained in $postNames are copied in
	 * this. So you can access it by $this->myPostVariable.
	 *
	 * @param  Array $postNames name of the variables expected to be posted
	 * @return Boolean returns true if all the variables were present
	 */
	protected function expectsPost($postNames){

		foreach ($postNames as $postName) {

			//If the variable is NULL (not posted), we log and exit
			if($this->input->post($postName) === NULL){
				log_message('info', "Was expecting " . print_r($postNames, true) .
					" got " . print_r($_POST, true));
				return false;
			}

			$cleanedValue = htmlspecialchars(
							htmlentities(
							$this->security->xss_clean(
							strip_tags(
								$this->input->post($postName, true)
							))));

			if((is_numeric($cleanedValue) && is_finite($cleanedValue))
				|| !is_numeric($cleanedValue)){

				$this->{$postName} = $cleanedValue;
			}else{
				return false;
			}
		}

		return true;
	}

	/**
	 * Intercepts all method call on controller in order to provide logging
	 * capabilities.
	 *
	 * @param  String $method the method name
	 * @param  Array  $params An array of get parameters (segments following the
	 * method segment)
	 * @return mixed Result of the method call
	 */
	public function _remap($method, $params = array()){

		//Entry logging
		$begin = new DateTime();
		log_message('info', 'Entering ' . get_class($this).':'
			.$method .'->'.print_r($params, true));

		//method execution
		$result = call_user_func_array(array($this, $method), $params);

		//Exit logging
		$end = new DateTime();
		$diff = $begin->diff($end);
		log_message('info', 'Exiting ' . get_class($this).':'
			.$method .' after '.$diff->format( '%H:%I:%S' ).'->'
			.print_r($result, true)
		);
	}

	public function __destruct() {  
	    $this->db->close();  
	}  

}
