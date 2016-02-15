<?php
class MY_Exceptions extends CI_Exceptions {

    public function __construct()
    {
      parent::__construct();
    }

    public function show_php_error($severity, $message, $filepath, $line)
    {
    	$severity = ( ! isset($this->levels[$severity])) ? $severity : $this->levels[$severity];
    	$filepath = str_replace("\\", "/", $filepath);

      $header = $severity. ' exception
      . occurred with message: '.$message
      .' in File '.$filepath
      .' at Line '.$line
      .' at URL  '.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

      $data = 'payload={"text": "'.$header.'"}';

      $ch = curl_init(exception_url());

      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
          'Content-Length: '.strlen($data))
      );

      $result = curl_exec($ch);
    }
}
