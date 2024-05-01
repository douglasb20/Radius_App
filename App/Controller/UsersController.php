<?php

namespace App\Controller;

use App\Classes\UsersClass;
use App\Exceptions\CommomException;

class UsersController extends Controller
{
  public function Index()
  {
    $this->CheckSession();
    
    $this->setTituloPagina("Página inicial");
    $this->setClassDivContainer("container");

    $this->render("Users");
  }

  public function ListUsers()
  {
    $this->CheckSession();
    $input = $this->getPost();
    $users = (new UsersClass)->ListUsers($input);
    $this->data = $users;
  }

  /**
  * Função para adicionar novo usuário
  * @author Douglas A. Silva
  */
  public function AdicionarUsuario(){
    $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    $fields = $this->getPost();
    (new UsersClass)->AdicionarUsuario($fields);
    $this->masterMysqli->commit();
  }

  /**
  * Função para adicionar novo usuário
  * @author Douglas A. Silva
  */
  public function AtualizaUsuario(){
    $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    $fields = $this->getPost();
    (new UsersClass)->AtualizaUsuario($fields);
    $this->masterMysqli->commit();
  }

  
  /**
   * Função para solicitar nova senha
   * @author Douglas A. Silva
   * @return void
   */
  public function SendRequestPassword()
  {
    try {
      $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
      $email = $this->getQuery("email");
      (new UsersClass)->SendRequestPassword($email);

      $this->masterMysqli->commit();
    } catch (\Exception $e) {
      $this->masterMysqli->rollback();
      throw $e;
    }
  }

  /**
    * Função para criar nova senha
    * @author Douglas A. Silva
    * @return void
    */
    public function RecoverPassword(){
      try{
          $dados = [];
          $user_token = decrypt($this->getQuery("forgot_token"));
          $dados = (new UsersClass)->RecoverPassword($user_token);

          $this
          ->setTituloPagina("Recuperação de senha")
          ->setClassDivContainer("container d-flex justify-content-center align-items-start pt-5 h-100")
          ->render("Login.recoverPassword", $dados);
      }catch(\Exception $e){
          throw $e;
      }
  }

  /**
  * Função para criar nova senha
  * @author Douglas A. Silva
  * @return void
  */
  public function RequestRecover(){
      try{
          $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
          extract($this->getPost());
          $id = $this->getQuery("id_user");
          
          (new \App\Classes\UsersClass)->UpdateUserPassword($id, $password);

          $this->masterMysqli->commit();
      }catch(\Exception $e){
          $this->masterMysqli->rollback();
          throw $e;
      }
  }
}
