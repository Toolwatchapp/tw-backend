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
      'index_get' => ['key' => true, 'limit' => 60],
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
      $this->ip_throttle = new MY_Model("limits_ip", 'ip');
    }

    public function index_options(){
        $this->response(null, REST_Controller::HTTP_OK);
    }

    /**
     * Retrieve an user using his API key
     * @return [type] [description]
     */
    public function index_get(){

      if($this->rest->user_id !== NULL){
        $user = $this->user->getUser($this->rest->user_id);

        $user->watches = $this->measure->getNLastMeasuresByUserByWatch($user->userId);
        $this->response($user, REST_Controller::HTTP_OK);
        
      }
    }

    /**
     * Login endpoint
     */
    public function index_put()
    {
        if(!$this->throttleIP('index_put')){
          $email = $this->put('email');
          $password = $this->put('password');
          $this->loginAndAuth($email, $password);
        }else{
          $this->response(["message" => "api limit reached"],
          REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    /**
    * Fetches current amount of API calls without authenfication
    * 
    * @param the method invoked
    * @return bool. True if the IP should be throttled. False otherwise
    */
    private function throttleIP($method){

        $ip = $_SERVER['REMOTE_ADDR'];
        $data = array(
          'ip'=> $ip,
          'hour_started >=' => (time() - 3600)
        );

        $limit = $this->ip_throttle->select("count")->find_by($data);

        //IP not present for the last hour
        if($limit == false){

          $reset = $this->ip_throttle->update($ip, array('hour_started' => time(), 'count' => 1))
                   && $this->ip_throttle->affected_rows() === 1;

          //Reset failled (i.e. first time we see that ip)
          if(!$reset){
              $this->ip_throttle->insert(array('ip'=> $ip, 'hour_started' => time(), 'count' => 1));
              log_message('info', $this->ip_throttle->inserted_id());
          }

          return false;
        }
        //ip over or at the limit
        else if($limit->count >= $this->methods[$method]['limit']){
          return true;
        }
        //ip below the limit
        else{
          $this->ip_throttle->raw_sql('Update limits_ip set count = count + 1 where ip = \'' . $ip . '\' and hour_started >=' . (time() - 3600));
          return false;
        }

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

      if(!$this->throttleIP('index_post')){

        $email       = $this->post('email');
        $password    = $this->post('password');
        $lastname    = $this->post('lastname');
        $firstname   = $this->post('name');
        $country     = $this->post('country');

        //If the email isn't already in used
        if (!$this->user->checkUserEmail($email)) {

          // Create the account
          if ($this->user->signup($email, $password, $lastname, $firstname, $country)) {

            $this->loginAndAuth($email, $password);
          }
        //The email is already in use
        } else {
          $this->response(["message" => "email taken"],
            REST_Controller::HTTP_UNAUTHORIZED);
        }
      }else{
         $this->response(["message" => "api limit reached"],
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
