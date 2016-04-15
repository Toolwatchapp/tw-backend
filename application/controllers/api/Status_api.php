<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Status_api extends MY_Controller {

  public function index(){

    $factory = new Ejsmont\CircuitBreaker\Factory();
    $circuitBreaker = $factory->getSingleApcInstance(1, 300);

    echo json_encode([
      "database"=>$circuitBreaker->isAvailable("database"),
      "database_slave"=>$circuitBreaker->isAvailable("databaseSlave"),
      "database_reporting"=>$circuitBreaker->isAvailable("database_reporting"),
      "mandrill"=>$circuitBreaker->isAvailable("mandrill"),
      "mailchimp"=>$circuitBreaker->isAvailable("mailchimp"),
      "slack"=>$circuitBreaker->isAvailable("slack"),
      "google_calendar"=>$circuitBreaker->isAvailable("google_calendar")
    ]);
  }
}

?>
