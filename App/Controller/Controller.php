<?php

namespace App\Controller;

use App\Exceptions\CommomException;

class Controller extends \Core\Defaults\DefaultController
{
  public function errorPage()
  {
    $this->setTituloPagina("Página inicial");
    $this->setClassDivContainer("container-fluid p-0");

    $this->render("404");
  }

  public function CheckSession()
  {
    try {
      if (!$this->validateAuth()) {
        route()->redirect("login");
        die();
      }
    } catch (\Exception $e) {
      throw $e;
    }
  }
}
