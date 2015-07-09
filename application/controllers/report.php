<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends MY_Controller 
{
    public function __construct()
	{
		$this->_needLoggedIn = true;
		parent::__construct();
		$this->load->model('watch');
        $this->load->model('measure');
        $this->load->model('user');
	}

	function index()
	{

		
		$this->_headerData['headerClass'] = 'blue';
        $this->load->view('header', $this->_headerData);

        $this->_bodyData['events'] = $this->event->getEvents();
        $this->_bodyData['eventsColumns'] = $this->event->EVENT_STRING;
        $this->_bodyData['allEvents'] = $this->event->getAllEvents();

        $this->_bodyData['measure'] = $this->measure->count_all();
        $this->_bodyData['watch'] = $this->watch->count_all();
        $this->_bodyData['user'] = $this->user->count_all();
        $this->_bodyData['domain'] = $this->user->statsDomain();
        $this->_bodyData['platforms'] = $this->event->getPlatforms();
        $this->_bodyData['browsers'] = $this->event->getBrowsers();
        $this->_bodyData['mobile'] = $this->event->mobileEvents();


		$this->load->view('report', $this->_bodyData);
		$this->load->view('footer');
	}

}