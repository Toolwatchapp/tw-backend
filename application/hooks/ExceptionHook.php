<?php
class ExceptionHook
{
  public function SetExceptionHandler()
  {
    set_exception_handler(array($this, 'HandleExceptions'));
  }

  public function HandleExceptions($exception)
  {

  	$header ='Exception of type \''.get_class($exception)
    .'\' occurred with Message: '.$exception->getMessage()
    .' in File '.$exception->getFile()
    .' at Line '.$exception->getLine()
    .' at URL  '.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

    $msg ="\r\n Backtrace \r\n";
	  $msg .=$exception->getTraceAsString();

    log_message('error', $header.$msg, TRUE);

    $data = '{"text": "'.$header.'```'.$msg.'```"}';

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
