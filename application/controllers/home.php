<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
        array_push($this->_headerData['javaScripts'], "watch.animation");
		$this->load->view('header', $this->_headerData);
		$this->load->view('home');
		$this->load->view('footer');
	}
	 
    function logout()
    {
        $this->user->logout();
        redirect(base_url());
    }
    
    function resetPassword($resetToken='')
	{    
        $this->_headerData['headerClass'] = 'blue';
        $this->load->view('header', $this->_headerData);

        $this->_bodyData['resetToken'] = $resetToken;
        $this->load->view('reset-password', $this->_bodyData);

        $this->load->view('footer');  
	}
    
    function signupEmail()
    {
        $this->_bodyData['resetToken'] = 'a4f9g53F47gF';
        $this->load->view('email/reset-password', $this->_bodyData);
    }
    
    function about()
    {
        $this->_headerData['headerClass'] = 'blue';
        $this->_headerData['title'] = 'About Toolwatch';
        $this->load->view('header', $this->_headerData);
		$this->load->view('about');
		$this->load->view('footer');
    }
    
    /*function help()
    {
        $this->_headerData['headerClass'] = 'blue';
        $this->load->view('header', $this->_headerData);
		$this->load->view('help');
		$this->load->view('footer');
    }*/
    
    function contact()
    {
        $this->_headerData['headerClass'] = 'blue';
        $this->_headerData['title'] = 'Contact';
        $this->load->view('header', $this->_headerData);
		$this->load->view('contact');
		$this->load->view('footer');
    }
    
    function watchTips()
    {
        $this->_headerData['headerClass'] = 'blue';
        $this->load->view('header', $this->_headerData);
		$this->load->view('watch-tips');
		$this->load->view('footer');
    }    
}