<?php

namespace App\Exceptions;

class UnauthorizedException extends \Exception{
  public function __construct($message = "", $code = 401, \Throwable $previous = null){
    if(empty($message) ){
      $message = "Unauthorized access";
    }
    parent::__construct($message, $code, $previous);
  }
}