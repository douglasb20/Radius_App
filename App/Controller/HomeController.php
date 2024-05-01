<?php

namespace App\Controller;

class HomeController extends Controller
{
  public function Index()
  {

    // if ($this->validateAuth()) {
    //   route()->redirect("/");
    //   return;
    // }
    $this->setTituloPagina("Página inicial");
    $this->setClassDivContainer("container");

    $this->render("Users");
  }
}
