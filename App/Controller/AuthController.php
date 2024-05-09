<?php

namespace App\Controller;

use App\Classes\OperatorsClass;
use App\Exceptions\UnauthorizedException;

class AuthController extends Controller
{
  public function Index()
  {
    try {
      if ($this->validateAuth()) {
        route()->redirect("/");
        return;
      }

      $this
        ->setTituloPagina("Login")
        ->setClassDivContainer("container d-flex justify-content-center align-items-center h-100")
        ->render("Login");
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function AuthLogin()
  {
    try {
      $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
      extract($this->getPost("username"));

      (new OperatorsClass)->AuthenticateLoginUser($username, $password);

      $this->masterMysqli->commit();
    } catch (\Exception $e) {
      $this->masterMysqli->rollback();
      throw $e;
    }
  }

  public function Logout(){
    (new OperatorsClass)->Logout();
    route()->redirect( route()->link('login-page') );
  }
  
}
