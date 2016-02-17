<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function sendExceptionToSlack($message){

  $data = json_encode(["text"=>$_SERVER['HTTP_HOST']."\r\n".$message]);

  $ch = curl_init(exception_url());

  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: '.strlen($data))
  );

  $result = curl_exec($ch);

  log_message("info", "slack exception:".print_r($result, true));
}
?>
