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

      sendExceptionToSlack($header);
    }
}
