<?php

namespace App\Controller;

use App\Exceptions\CommomException;
use App\Exceptions\UnauthorizedException;

class Controller extends \Core\Defaults\DefaultController
{
  public function errorPage()
  {
    $this->setTituloPagina("Página inicial");
    $this->setClassDivContainer("container-fluid p-0");

    $this->render("404");
  }

  public function CheckSession($redirect = false)
  {
    if (!$this->validateAuth()) {
      if($redirect){
        route()->redirect("login");
        die();
      }

      throw new UnauthorizedException("Sua sessão foi expirada, recarregue a página para renovar sua sessão");
    }
  }
}
