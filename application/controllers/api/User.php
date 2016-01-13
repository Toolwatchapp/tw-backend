<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

class Users extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model("Key");
    }

    public function auth_put()
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

            $user["key"] = $key;
            $this->response($user, REST_Controller::HTTP_OK);
          }else{
            $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
          }
        }else{
          $this->response(NULL, REST_Controller::HTTP_UNAUTHORIZED);
        }
      }else{
        $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
      }

    }

    public function create_post()
    {
      echo "qlkzdqdn";
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

				} else {

          $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
				}
			//The email is already in use
			} else {
        $this->response(["message" => "email"],
          REST_Controller::HTTP_UNAUTHORIZED);
			}
    }

    public function users_delete()
    {
        $id = (int) $this->get('id');

        // Validate the id.
        if ($id <= 0)
        {
            // Set the response and exit
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        // $this->some_model->delete_something($id);
        $message = [
            'id' => $id,
            'message' => 'Deleted the resource'
        ];

        $this->set_response($message, REST_Controller::HTTP_NO_CONTENT); // NO_CONTENT (204) being the HTTP response code
    }

}
