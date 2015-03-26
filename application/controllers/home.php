<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
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
    
    function testMail()
    {
       /* date_default_timezone_set('Europe/Paris');
        $this->load->library('email');
                
        $config['protocol'] = "smtp";
        $config['smtp_host'] = "smtp.mandrillapp.com";
        $config['smtp_port'] = "587";
        $config['smtp_user'] = "marc@toolwatch.io"; 
        $config['smtp_pass'] = "pUOMLUusBKdoR604DpcOnQ";
        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['newline'] = "\r\n";

        $this->email->initialize($config);

        $this->email->from('hello@toolwatch.io', 'Toolwatch');
        $this->email->to('size10@yopmail.com', '');
        $this->email->reply_to('hello@toolwatch.io', 'Toolwatch');
        $scheduleTime = time()+86400;
        //$sentAt = date('Y-', $currentTime).date('m-', $currentTime).(date('d', $currentTime)+1).date(' H:i:s', $currentTime);
        $sentAt = date('Y-', $scheduleTime).date('m-', $scheduleTime).(date('d', $scheduleTime)).' '.(date('H', $scheduleTime)-1).':'.(date('i', $scheduleTime)).date(':s', $scheduleTime);
        echo $sentAt;
        $this->email->add_custom_header('X-MC-SendAt',$sentAt); 
        
        

        $this->email->subject('test mail schedule');


        $this->email->message('toto');

        if($this->email->send())
        {
           echo 'ok<br>'; 
            echo $this->email->print_debugger(); 
        }
        else
        {
            echo $this->email->print_debugger(); 
        }     */
        
        $user = $this->user->getUserFromWatchId(1);
        
        var_dump($user);
    }
    
    
}