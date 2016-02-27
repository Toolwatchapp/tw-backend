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
      .' at Line '.$line;

      $data = json_encode(["text"=>$_SERVER['HTTP_HOST']."\r\n".$header]);

      $ch = curl_init(getenv("SLACK_EXCEPTION"));

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
}
