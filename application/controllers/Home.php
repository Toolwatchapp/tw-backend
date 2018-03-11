<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

class Home extends MY_Controller {

	private $viewName;

	function __construct() {
		parent::__construct();

		$this->load->model('measure');


		$this->viewName = $this->agent->is_mobile()? "home/home-mobile": "home/home";
	}

	function index() {

		$this->agent->is_mobile()? array_push($this->_headerData['javaScripts'], "home.logic.mobile"): array_push($this->_headerData['javaScripts'], "home.logic", "watch.animation", "time.api");

		$this->load->view('header', $this->_headerData);
		$this->load->view($this->viewName, $this->homeMessage());
		$this->load->view('footer');
	}

	function result() {

		$this->_headerData["meta_img"] = img_url("accuracy.jpg");
		$this->index();
	}
	
	function mobile() {
		$this->load->view('mobile-landing', $this->_headerData);	
	}

	private function homeMessage() {

		$randBrands  = rand(0, 2);
		$randWatches = rand(0, 2);

		$watchBrands = array('Seiko', 'Rolex', 'Omega');
		$videos      = array('Omega', 'Rolex', 'Vacheron');

		if (!$this->user->isLoggedIn()) {

			$homePage = 'HOME_PAGE_'.$randWatches;
			$this->event->add($homePage);
		}

		$title = $this->measure
			->getMeasuresCountByWatchBrand($watchBrands[$randBrands]).
			' '.$watchBrands[$randBrands].' measured on Toolwatch.io';


		$url = $this->agent->is_mobile()? img_url($videos[$randWatches]).'.png': vid_url($videos[$randWatches]).'.mp4';

		return array('title'=>$title, 'video_url'=>$url);
	}

	function logout() {

		if($this->input->method(TRUE) === 'POST'){
			$this->user->logout();
			echo json_encode(true);
		}else{
			echo json_encode(false);
		}
	}

	function resetPassword($resetToken = '') {
		$this->_headerData['headerClass'] = 'blue';

		$this->_headerData['metas'] = array(
			'<meta name="robots" content="noindex,nofollow">',
			'<meta name="referrer" content="never">'
		);
		$this->load->view('header', $this->_headerData);

		$this->_bodyData['resetToken'] = $resetToken;
		$this->load->view('reset-password', $this->_bodyData);

		$this->load->view('footer');
	}

	function about() {
		$this->_headerData['headerClass'] = 'blue';
		$this->_headerData['title']       = 'About Toolwatch';
		$this->_headerData['meta_description'] = 'Toolwatch is where
		watch aficionados measure the accuracy and precision of their watch.
		More than 5000 people use Toolwatch to take care of their watch.';
		$this->load->view('header', $this->_headerData);
		$this->load->view('about');
		$this->load->view('footer');
	}

	function contact() {
		$this->_headerData['headerClass'] = 'blue';
		$this->_headerData['title']       = 'Contact';
		$this->_headerData['meta_description'] = 'Contact the Toolwatch
		Team. We are here to answer questions about watch accuracy,
		precision, maintenance for watches and lots of other
		interesting topics.';
		$this->load->view('header', $this->_headerData);
		$this->load->view('contact');
		$this->load->view('footer');
	}
}
