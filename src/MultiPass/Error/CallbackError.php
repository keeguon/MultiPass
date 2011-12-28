<?php

namespace MultiPass\Error;

class CallbackError extends \Exception
{
  public
      $error        = null
    , $error_reason = null
    , $error_uri    = null
  ;

  public function __construct($error, $error_reason, $error_uri)
  {
    $this->error        = $error;
    $this->error_reason = $error_reason;
    $this->error_uri    = $error_uri;
  }
}
