<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require_once APPPATH . '/libraries/REST_Controller.php';

class Users_api extends REST_Controller {

    /**
     * Defines which methods are protected by
     * an API key
     * @var Array
     */
    protected $methods = [
      'index_put' => ['key' => false, 'limit' => 20],
      'index_post' => ['key' => false, 'limit' => 20],
      'index_delete' => ['key' => true, 'limit' => 20],
      'index_options' => ['key' => false],
     ];
     /**
      * Default constructor
      */
    public function __construct(){

      parent::__construct();
      $this->load->model("key");
      $this->load->model("measure");
    }

    public function index_options(){
        $this->response(null, REST_Controller::HTTP_OK);
    }

    /**
     * Login endpoint
     */
    public function index_put()
    {
        $email = $this->put('email');
        $password = $this->put('password');

        $this->loginAndAuth($email, $password);
    }

    /**
     * Fetches an user according to $email and $password.
     * Create or refresh API key for the given user.
     *
     * @param  String $email
     * @param  String $password
     * @return HTTP_OK
     * @return HTTP_UNAUTHORIZED (login failed)
     * @return HTTP_BAD_REQUEST (if $email or $password is missing)
     */
    private function loginAndAuth($email, $password){

      if($email !== NULL && $password !== NULL){

        $user = $this->user->login($email, $password);

        if($user !== false){

          $key = $this->key->generate_key($user);

          if($key !== false){

            $user->key = $key;
            $user->watches = $this->measure->getNLastMeasuresByUserByWatch($user->userId);
            $this->response($user, REST_Controller::HTTP_OK);
          }
        }else{
          $this->response(NULL, REST_Controller::HTTP_UNAUTHORIZED);
        }
      }else{
        $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
      }

    }

    /**
     * Signup enpoints
     * @return HTTP_BAD_REQUEST if email is taken
     */
    public function index_post()
    {
      $email       = $this->post('email');
      $password    = $this->post('password');
      $name        = $this->post('name');
      $firstname   = $this->post('firstname');
      $country     = $this->post('country');

      //If the email isn't already in used
			if (!$this->user->checkUserEmail($email)) {

				// Create the account
				if ($this->user->signup($email, $password, $name, $firstname, $country)) {

					$this->loginAndAuth($email, $password);
				}
			//The email is already in use
			} else {
        $this->response(["message" => "email taken"],
          REST_Controller::HTTP_UNAUTHORIZED);
			}
    }

    /**
     * Delete an user
     * @return HTTP_INTERNAL_SERVER_ERROR (delete failed)
     * @return HTTP_NO_CONTENT on success
     */
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
