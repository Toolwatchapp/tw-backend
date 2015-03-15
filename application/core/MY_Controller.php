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
		$this->_headerData['javaScripts'] = array('jquery.min', 'bootstrap.min', 'application');
        $this->_headerData['headerClass'] = '';
        
        if($this->_needLoggedIn && !$this->user->isLoggedIn())
        {
            redirect(base_url());   
        }
	}

}