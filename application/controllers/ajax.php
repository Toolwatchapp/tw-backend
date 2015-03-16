<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller 
{
    function __construct()
    {
        parent::__construct();
        
        date_default_timezone_set('Europe/Paris');
    }
    function login()
    {
        if($this->input->post('email') && $this->input->post('password'))
        {
            $email = $this->input->post('email');   
            $password = $this->input->post('password');   
            if($this->user->login($email, $password))
            {
                echo 'SUCCESS';
            }
            else
            {
                echo 'ERROR';
            }
        }
    }
    
    function checkEmail()
    {
        if($this->input->post('email'))
        {
            if(!$this->user->checkUserEmail($this->input->post('email')))
            {
                echo 'SUCCESS';
            }
            else
            {
                echo 'ERROR';
            }
        }
    }
    
    function signup()
    {
        if($this->input->post('email'))
        {
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $name = $this->input->post('name');
            $firstname = $this->input->post('firstname');
            $timezone = $this->input->post('timezone');
            $country = $this->input->post('country');
            
            if($this->user->signup($email, $password, $name, $firstname, $timezone, $country))
            {
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
                $this->email->to($email, $name.' '.$firstname);
                $this->email->reply_to('hello@toolwatch.io', 'Toolwatch');

                $this->email->subject('Welcome to Toolwatch!');
                
                $message = $this->load->view('email/signup', '', true);
                $this->email->message($message);

                if($this->email->send())
                {
                   echo 'SUCCESS';   
                }
                else
                {
                    echo 'ERROR';   
                } 
            }
            else
            {
                echo 'ERROR';
            }
        }
    }
    
    function askResetPassword()
    {
        if($this->input->post('email'))
        {
            $email = $this->input->post('email');
            
            $resetToken = $this->user->askResetPassword($email);
            
            if($resetToken != '')
            {
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
                $this->email->to($email, $name.' '.$firstname);
                $this->email->reply_to('hello@toolwatch.io', 'Toolwatch');

                $this->email->subject('Your Toolwatch password');
                
                $data['resetToken'] = $resetToken;
                
                $message = $this->load->view('email/reset-password', $data, true);
                $this->email->message($message);

                if($this->email->send())
                {
                   echo 'SUCCESS';   
                }
                else
                {
                    echo 'ERROR';   
                }                 
            }
            else
            {
                echo 'ERROR';   
            }
        }
    }
    
    function resetPassword()
    {
        if($this->input->post('resetToken'))
        {
            $resetToken = $this->input->post('resetToken');
            $password = $this->input->post('password');
            
            if($this->user->resetPassword($resetToken, $password))
            {
                echo 'SUCCESS';
            }
            else
            {
               echo 'ERROR';
            }
        }   
    }
    
    function getReferenceTime()
    {
        $this->session->set_userdata('referenceTime', time());   
    }
    
    function newMeasure()
    {
        if($this->input->post('watchId'))
        {
            $watchId = $this->input->post('watchId');
            $referenceTime = $this->session->userdata('referenceTime');
            $userTimezone = $this->input->post('userTimezone');
                        
            $tempUserTime = preg_split('/:/', $this->input->post('userTime'));
            
            
            $userTime = mktime($tempUserTime[0], $tempUserTime[1], $tempUserTime[2], date("n"), date("j"), date("Y"));
            
            $this->load->model('measure');
            
            if($this->measure->newMeasure($watchId, $referenceTime, $userTime))
            {
                echo 'SUCCESS';
            }
            else
            {
                echo 'ERROR';   
            }
        }
    }
    
    function contact()
    {
        if($this->input->post('name'))
        {
            $name = $this->input->post('name');
            $email = $this->input->post('email');
            $message = $this->input->post('message');
            
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

            $this->email->from('contact@toolwatch.io', 'Toolwatch contact');
            $this->email->to('marc@toolwatch.io', 'Marc');
            $this->email->reply_to($email, $name);

            $this->email->subject('Contact Toolwatch from '.$name);

            $bodyMessage ='<b>Name :</b> '.$name.'<br>';
            $bodyMessage .= '<b>Email :</b> '.$email.'<br>';
            $bodyMessage .= '<b>Message :</b> <br>';
            $bodyMessage .= $message;
            
            $this->email->message($bodyMessage);

            if($this->email->send())
            {
               echo 'SUCCESS';   
            }
            else
            {
                echo 'ERROR';   
            }  
        }
    }
                
}