<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');
}

class Home extends MY_Controller {

	//TODO: Can we overide load view to append .mobile ?
	private $viewName = "home/home";

	function __construct() {
		parent::__construct();
		$this->load->model('measure');

		if ($this->agent->is_mobile()) {
			$this->viewName = "home/home-mobile";
		}

	}

	function index() {

		if (!$this->agent->is_mobile()) {
			array_push($this->_headerData['javaScripts'], "home.logic", "watch.animation");
		} else {
			array_push($this->_headerData['javaScripts'], "home.logic.mobile");
		}

		$this->load->view('header', $this->_headerData);
		$this->load->view($this->viewName, $this->homeMessage());
		$this->load->view('footer');
	}

	function result() {

		if (!$this->agent->is_mobile()) {
			array_push($this->_headerData['javaScripts'], "home.logic", "watch.animation");
		} else {
			array_push($this->_headerData['javaScripts'], "home.logic.mobile");
		}

		$this->_headerData["meta_img"] = img_url("accuracy.jpg");

		$this->load->view('header', $this->_headerData);
		$this->load->view($this->viewName, $this->homeMessage());
		$this->load->view('footer');
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

		if (!$this->agent->is_mobile()) {
			return array('title' => $this->measure
				->getMeasuresCountByWatchBrand($watchBrands[$randBrands]).
				' '.$watchBrands[$randBrands].' measured on Toolwatch.io',
				'video_url' => vid_url($videos[$randWatches]).'.mp4');
		} else {
			return array('title' => $this->measure
				->getMeasuresCountByWatchBrand($watchBrands[$randBrands]).
				' '.$watchBrands[$randBrands].' measured on Toolwatch.io',
				'video_url' => img_url($videos[$randWatches]).'.png');
		}

	}

	function logout() {

		$this->user->logout();
		redirect(base_url());
	}

	function resetPassword($resetToken = '') {
		$this->_headerData['headerClass'] = 'blue';
		$this->load->view('header', $this->_headerData);

		$this->_bodyData['resetToken'] = $resetToken;
		$this->load->view('reset-password', $this->_bodyData);

		$this->load->view('footer');
	}

	function about() {
		$this->_headerData['headerClass'] = 'blue';
		$this->_headerData['title']       = 'About Toolwatch';
		$this->load->view('header', $this->_headerData);
		$this->load->view('about');
		$this->load->view('footer');
	}

	function contact() {
		$this->_headerData['headerClass'] = 'blue';
		$this->_headerData['title']       = 'Contact';
		$this->load->view('header', $this->_headerData);
		$this->load->view('contact');
		$this->load->view('footer');
	}
}
