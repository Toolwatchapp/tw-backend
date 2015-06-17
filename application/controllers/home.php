<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller 
{
	function __construct()
	{
		parent::__construct();
        $this->load->model('measure');
	}
	
	function index()
	{

        if(!$this->agent->is_mobile()){
            array_push($this->_headerData['javaScripts'], "home.logic", "watch.animation");
        }else{
             array_push($this->_headerData['javaScripts'], "home.logic.mobile");
        }

		$this->load->view('header', $this->_headerData);
		$this->load->view('home', $this->homeMessage());
		$this->load->view('footer');
	}

    private function homeMessage(){

        $rand = rand ( 0 , 3 );

        $watchBrands = array('Seiko', 'Rolex', 'Omega');
        $videos = array('Omega.mp4', 'Rolex.mp4', 'Zenith.mp4');

        $video = vid_url('Zenith.mp4');

        if($rand >= 0 && $rand <= 2){
            return array('title'=>$this->measure
                ->getMeasuresCountByWatchBrand($watchBrands[$rand]) . 
                ' ' . $watchBrands[$rand] . ' measured on Toolwatch.io',
                'video_url'=>vid_url($videos[$rand]));
        }else{
            return array('title'=>$this->measure->getMeasuresWeeklyAverageAccuracy() .
                ' spd average accuracy measured this week', 'video_url'=>$video);
        }
    }
	 
    function logout()
    {
        $this->event->add($this->event->LOGOUT);

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