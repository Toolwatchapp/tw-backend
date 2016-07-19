<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require_once APPPATH . '/libraries/REST_Controller.php';

class Watches_api extends REST_Controller {

  /**
   * Defines which methods are protected by
   * an API key
   * @var Array
   */
  protected $methods = [
    'index_put' => ['key' => true, 'limit' => 60],
    'index_post' => ['key' => true, 'limit' => 60],
    'index_delete' => ['key' => true, 'limit' => 60],
    'index_get' => ['key' => true, 'limit' => 600],
    'brands_get' => ['key' => true, 'limit' => 600],
    'models_get' => ['key' => true, 'limit' => 600],
    'index_options' => ['key' => false]
   ];

   /**
    * Default constructor
    */
  public function __construct(){
    parent::__construct();
    $this->load->model("key");
    $this->load->model("watch");
    $this->load->model("measure");
  }

  public function index_options(){
    $this->response(null, REST_Controller::HTTP_OK);
  }

  /**
   * returns the watches and their latest measures.
   *
   * @return JSON Watches and measure
   */
  public function index_get(){

    $this->response(
      $this->measure->getNLastMeasuresByUserByWatch($this->rest->user_id),
      REST_Controller::HTTP_OK
    );
  }

  /**
   * Update a watch
   * @return HTTP_BAD_REQUEST if arguments are missing
   */
  public function index_put()
  {

    $watchId = $this->put('id');
    $brand = $this->put('brand');
    $name = $this->put('name');
    $yearOfBuy = $this->put('yearOfBuy');
    $serial = $this->put('serial');
    $caliber = $this->put('caliber');

    if($watchId !== NULL && $brand !== NULL && $name !== NULL &&
       $serial !== NULL && $caliber !== NULL){

      if($this->watch->editWatch($this->rest->user_id, $watchId,
        $brand, $name, $yearOfBuy, $serial, $caliber)){

          $this->response(["success"=>true],
            REST_Controller::HTTP_OK
          );
      }

    }else{
      $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
    }
  }

  /**
   * Create new watch
   * @return HTTP_BAD_REQUEST if arguments are missing
   */
  public function index_post()
  {
    $brand = $this->post('brand');
    $name = $this->post('name');
    $yearOfBuy = $this->post('yearOfBuy');
    $serial = $this->post('serial');
    $caliber = $this->post('caliber');

    if($brand !== NULL && $name !== NULL &&
       $serial !== NULL && $caliber !== NULL){

      $id = $this->watch->addWatch($this->rest->user_id, $brand, $name,
      $yearOfBuy, $serial, $caliber);

      if($id){

        $this->response(
          ["id"=>$id],
          REST_Controller::HTTP_OK
        );
      }

    } else {
      $this->response(NULL,
        REST_Controller::HTTP_BAD_REQUEST);
    }
  }

  /**
   * Autocompletes brand from 2 letters
   * @return JSON Array Brand matching $brand arg
   */
  public function brands_get($partialBrand = NULL){

    if(is_string($partialBrand) && strlen($partialBrand) >= 1){

      $partialBrand = strtolower($partialBrand);

      $brands = json_decode(file_get_contents(APPPATH.'../assets/json/watch-brand.json'));

      $matchingBrands = [];

      foreach ($brands as $brand) {

        if (strpos(strtolower($brand->name), $partialBrand) !== false) {
            array_push($matchingBrands, $brand);
        }
      }

      $this->response($matchingBrands, REST_Controller::HTTP_OK);

    }
  }

  /**
   * Autocompletes model from 2 letters and a given brand
   * @return JSON Array Models matching $brand and $model arg
   */
  public function models_get($brand, $partialModel){

    if(is_string($brand) &&
      is_string($partialModel)
      && strlen($partialModel) >= 1
      && file_exists(APPPATH.'../assets/json/watch-models/'.$brand.'.json')){

      $partialModel = strtolower($partialModel);

      $models = json_decode(
        file_get_contents(
          APPPATH.'../assets/json/watch-models/'.$brand.'.json'
        )
      );

      $matchingModels = [];

      foreach ($models as $model) {

          if (strpos(strtolower($model), $partialModel) !== false) {

              array_push($matchingModels, $model);
          }
      }

      $this->response($matchingModels, REST_Controller::HTTP_OK);


    } else {
      $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
    }
  }

  /**
   * Delete a watch
   * @return HTTP_INTERNAL_SERVER_ERROR (delete failed)
   */
  public function index_delete()
  {

    $watchId = $this->delete('watchId');

    log_message("ERROR", $watchId);


    if($watchId !== NULL
      && $this->watch->deleteWatch($watchId, $this->rest->user_id)){

        $this->index_get();
    }else{
      $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST);
    }
  }

}

?>
