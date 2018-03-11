<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modal extends MY_Controller
{

    public function accuracyWarning(){
        if($this->input->post('ajax'))
        {
            $this->load->view('modal/accuracy-warning');
            $this->event->add(ACCURACY_WARNING_POPUP);
        }
        else
        {
            redirect(base_url());
        }
    }

	public function login()
	{
		if($this->input->post('ajax'))
		{
            $this->event->add(LOGIN_POPUP);
			$this->load->view('modal/login');
		}
        else
        {
            redirect(base_url());
        }
	}

	public function signUp()
	{
		if($this->input->post('ajax'))
		{
            $this->event->add(SIGN_UP_POPUP);
			      $this->load->view('modal/sign-up');
		}
        else
        {
            redirect(base_url());
        }
	}

    public function resetPassword()
    {
        if($this->input->post('ajax'))
		{
			$this->load->view('modal/reset-password');
		}
        else
        {
            redirect(base_url());
        }
    }
}
