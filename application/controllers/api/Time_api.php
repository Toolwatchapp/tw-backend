<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require_once APPPATH . '/libraries/REST_Controller.php';

class Time_api extends REST_Controller {

  protected $methods = [
    'index_get' => ['key' => false]
   ];

   /**
    * returns the watches and their latest measures.
    *
    * @return JSON Watches and measure
    */
   public function index_get(){

     $this->response(
       [time=>microtime(true)*1000],
       REST_Controller::HTTP_OK
     );
   }
}
