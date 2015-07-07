<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller 
{
	protected $_headerData = array();
	protected $_bodyData = array();
	protected $_footerData = array();
    protected $_needLoggedIn = false;

	public function __construct()
	{
		parent::__construct();

        $this->_headerData['userIsLoggedIn'] = $this->user->isLoggedIn();
		$this->_headerData['styleSheets'] = array('main');
		$this->_headerData['javaScripts'] = array('jquery.min', 'bootstrap.min', 'application', 'MediaElement/mediaelement-and-player.min', 
			'facebook');
        $this->_headerData['headerClass'] = '';


        if(!$this->user->isAdmin() && strpos(base_url(), "tw-prepod") !== false){
        	 if(!$this->input->post('pw')){
			echo "<form action='".base_url()."' method='post'><input name='pw' type='password'/><input type='submit'/>";
				die;
			}else if($this->input->post('pw') && $this->input->post('pw') === '&BSdJ88{waHK!Zj'){
				$this->session->set_userdata('admin', 'true');
			}else{
				die;
			}
        }

        if($this->_needLoggedIn && !$this->user->isLoggedIn())
        {
            redirect(base_url());   
        }
	}

}