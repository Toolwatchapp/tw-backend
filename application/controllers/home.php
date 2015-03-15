<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->load->view('header', $this->_headerData);
		$this->load->view('home');
		$this->load->view('footer');
	}
	 
    public function logout()
    {
        $this->user->logout();
        redirect(base_url());
    }
    
    public function resetPassword($resetToken='')
	{    
        $this->_headerData['headerClass'] = 'blue';
        $this->load->view('header', $this->_headerData);

        $this->_bodyData['resetToken'] = $resetToken;
        $this->load->view('reset-password', $this->_bodyData);

        $this->load->view('footer');  
	}
    
    public function signupEmail()
    {
        $this->_bodyData['resetToken'] = 'a4f9g53F47gF';
        $this->load->view('email/reset-password', $this->_bodyData);
    }
}