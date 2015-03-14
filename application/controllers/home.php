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
	
	public function login()
	{
		if($this->input->get('ajax'))
		{
			$this->load->view('login');
		}
	}
	
	public function signUp()
	{
		if($this->input->get('ajax'))
		{
			$this->load->view('sign-up');
		}
	}
	
	public function resetPassword()
	{
		if($this->input->get('ajax'))
		{
			$this->load->view('reset-password');
		}
	}
}