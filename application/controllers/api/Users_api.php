<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Users_api extends REST_Controller {

    protected $methods = [
      'index_put' => ['key' => false],
      'index_post' => ['key' => false],
      'index_delete' => ['key' => true]
     ];

    public function __construct(){
      parent::__construct();
      $this->load->model("key");
    }

    public function index_put()
    {
        $email = $this->put('email');
        $password = $this->put('password');

        $this->loginAndAuth($email, $password);
    }

    private function loginAndAuth($email, $password){

      if($email !== NULL && $password !== NULL){

        $user = $this->user->login($email, $password);

        if($user !== false){

          $key = $this->key->generate_key($user);

          if($key !== false){

            $user->key = $key;
            $this->response($user, REST_Controller::HTTP_OK);
          }
        }else{
          $this->response(NULL, REST_Controller::HTTP_UNAUTHORIZED);
        }
      }else{
        $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
      }

    }

    public function index_post()
    {
      $email       = $this->post('email');
      $password    = $this->post('password');
      $name        = $this->post('name');
      $firstname   = $this->post('firstname');
      $timezone    = $this->post('timezone');
      $country     = $this->post('country');

      //If the email isn't already in used
			if (!$this->user->checkUserEmail($email)) {

				// Create the account
				if ($this->user->signup(
						$email, $password, $name, $firstname,
						$timezone, $country)) {

					$this->loginAndAuth($email, $password);
				}
			//The email is already in use
			} else {
        $this->response(["message" => "email taken"],
          REST_Controller::HTTP_BAD_REQUEST);
			}
    }

    public function index_delete()
    {

      $responseCode = REST_Controller::HTTP_INTERNAL_SERVER_ERROR;

      if($this->user->delete($this->rest->user_id)
      && $this->key->delete($this->rest->key_id)){
        $responseCode = REST_Controller::HTTP_NO_CONTENT;
      }

      $this->response(NULL, $responseCode);
    }

}
