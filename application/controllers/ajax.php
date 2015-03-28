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
            $result = array();
            
            $email = $this->input->post('email');   
            $password = $this->input->post('password');   
            if($this->user->login($email, $password))
            {
                $result['success'] = true;
            }
            else
            {
                $result['success'] = false;
            }
            
            echo json_encode($result);
        }   
    }
    
    function checkEmail()
    {
        if($this->input->post('email'))
        {
            $result = array();
            
            if(!$this->user->checkUserEmail($this->input->post('email')))
            {
                $result['success'] = true;
            }
            else
            {
                $result['success'] = false;
            }
            
            echo json_encode($result);
        }
    }
    
    function signup()
    {
        if($this->input->post('email'))
        {
            $result = array();
            
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $name = $this->input->post('name');
            $firstname = $this->input->post('firstname');
            $timezone = $this->input->post('timezone');
            $country = $this->input->post('country');
            $mailingList = $this->input->post('mailingList');
            
            if($this->user->signup($email, $password, $name, $firstname, $timezone, $country))
            {
                
                if('true' == $mailingList)
                {
                    $this->load->helper('mcapi');
                    
                    $api = new MCAPI('eff18c4c882e5dc9b4c708a733239c82-us9');
                    $api->listSubscribe('7f94c4aa71', $email, ''); 
                }
                
                
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
                    $result['success'] = true;   
                    $this->user->login($email, $password);
                }
                else
                {
                    $result['success'] = false;   
                } 
            }
            else
            {
                $result['success'] = false;
            }
            
            echo json_encode($result);
        }
    }
    
    function askResetPassword()
    {
        if($this->input->post('email'))
        {
            $email = $this->input->post('email');
            
            $result = array();
            $data = array();
            
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
                $this->email->to($email, '');
                $this->email->reply_to('hello@toolwatch.io', 'Toolwatch');

                $this->email->subject('Your Toolwatch password');
                
                $data['resetToken'] = $resetToken;
                
                $message = $this->load->view('email/reset-password', $data, true);
                $this->email->message($message);

                if($this->email->send())
                {
                   $result['success'] = true;  
                }
                else
                {
                    $result['success'] = false; 
                }     
            }
            else
            {
                $result['success'] = false;   
            }
            
            echo json_encode($result);
        }
    }
    
    function resetPassword()
    {
        if($this->input->post('resetToken'))
        {
            $result = array();
            
            $resetToken = $this->input->post('resetToken');
            $password = $this->input->post('password');
            
            if($this->user->resetPassword($resetToken, $password))
            {
                $result['success'] = true;
            }
            else
            {
               $result['success'] = false;
            }
            
            echo json_encode($result);
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
            $result = array();
            
            $watchId = $this->input->post('watchId');
            $referenceTime = $this->session->userdata('referenceTime');
            $userTimezone = $this->input->post('userTimezone');
            $getAccuracy = $this->input->post('getAccuracy');
                        
            $tempUserTime = preg_split('/:/', $this->input->post('userTime'));
            
            
            $userTime = mktime($tempUserTime[0], $tempUserTime[1], $tempUserTime[2], date("n"), date("j"), date("Y"));
            
            $this->load->model('measure');
            
            if($this->measure->newMeasure($watchId, $referenceTime, $userTime))
            {
                $watchMeasures = $this->measure->getMeasures($watchId);
                if(sizeof($watchMeasures) == 1)
                {
                    
                    $user = $this->user->getUserFromWatchId($watchId);

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
                    $this->email->to($user->email, $user->name.' '.$user->firstname);
                    $this->email->reply_to('hello@toolwatch.io', 'Toolwatch');

                    $scheduleTime = time()+86400;
                    $sentAt = date('Y-', $scheduleTime).date('m-', $scheduleTime).(date('d', $scheduleTime)).' '.(date('H', $scheduleTime)-1).':'.(date('i', $scheduleTime)).date(':s', $scheduleTime);
                    $this->email->add_custom_header('X-MC-SendAt',$sentAt); 

                    $this->email->subject('It\'s time to check your watch\'s accuracy !');

                    $data['watchBrand'] = $user->brand;
                    $data['watchName'] = $user->name;

                    $message = $this->load->view('email/remind-check-accuracy', $data, true);
                    $this->email->message($message);

                    if($this->email->send())
                    {
                       $result['success'] = true; 
                    }
                    else
                    {
                        $result['success'] = false;   
                    } 
                }
                else
                {
                    $result['success'] = true;
                }
                
                if($getAccuracy == 'true')
                {
                    $result['data']['accuracy'] = $this->measure->getWatchAccuracy($watchId);   
                }
            }
            else
            {
                $result['success'] = false;   
            }
            
            echo json_encode($result);
        }
    }
    
    function contact()
    {
        if($this->input->post('name'))
        {
            $result = array();
            
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
               $result['success'] = true;   
            }
            else
            {
                $result['success'] = false;   
            }  
            
            echo json_encode($result);
        }
    }
                
}