<?php

namespace Core;

class Index extends Defaults\DefaultController
{
  function __construct()
  {
    try {
      $this->SetTypeAuth("session");
      Router::baseUrl($_ENV['BASE_URL']);
      Router::processRoute();
    } catch (\Exception $e) {
      if ($this->masterMysqli != null) {
        $this->masterMysqli->rollback();
      }
      $this->retorna($e);
    }
  }

  function __destruct()
  {
    try {
      if ($this->masterMysqli != null) {
        mysqli_close($this->masterMysqli);
      }
    } catch (\Exception $e) {
      $this->retorna($e);
    }
  }
}
