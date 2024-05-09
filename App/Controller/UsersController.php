<?php

namespace App\Controller;

use App\Classes\UsersClass;
use App\Exceptions\CommomException;

class UsersController extends Controller
{
  public function Index()
  {
    $this->CheckSession(true);

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
   * Função para pegar usuário
   */
  public function GetUser()
  {
    $this->CheckSession();
    $id = $this->getQuery('id');
    $user = (new UsersClass)->GetUser($id);
    $this->data = $user;
  }

  /**
   * Função para adicionar novo usuário
   * @author Douglas A. Silva
   */
  public function AdicionarUsuario()
  {
    $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    $this->CheckSession();
    $fields = $this->getPost();
    (new UsersClass)->AdicionarUsuario($fields);
    $this->masterMysqli->commit();
  }

  /**
   * Função para adicionar novo usuário
   * @author Douglas A. Silva
   */
  public function AtualizaUsuario()
  {
    $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    $this->CheckSession();
    $fields = $this->getPut();
    (new UsersClass)->AtualizaUsuario($fields);
    $this->masterMysqli->commit();
  }


  /**
   * Função para solicitar nova senha
   * @author Douglas A. Silva
   * @return void
   */
  public function RequestReset()
  {
    $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    $this->CheckSession();
    $id_user = $this->getQuery("id_user");
    (new UsersClass)->RequestReset($id_user);

    $this->masterMysqli->commit();
  }

  /**
   * Função para criar nova senha
   * @author Douglas A. Silva
   * @return void
   */
  public function RecoverPassword()
  {
    try {
      $dados = [];
      $user_token = decrypt($this->getQuery("forgot_token"));
      $dados = (new UsersClass)->RecoverPassword($user_token);

      $this
        ->setTituloPagina("Recuperação de senha")
        ->setClassDivContainer("container d-flex justify-content-center align-items-start pt-5 h-100")
        ->render("Login.recoverPassword", $dados);
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * Função para criar nova senha
   * @author Douglas A. Silva
   * @return void
   */
  public function RequestRecover()
  {
    try {
      $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
      extract($this->getPost());
      $id = $this->getQuery("id_user");

      (new \App\Classes\UsersClass)->UpdateUserPassword($id, $password);

      $this->masterMysqli->commit();
    } catch (\Exception $e) {
      $this->masterMysqli->rollback();
      throw $e;
    }
  }

  /**
   * Função para alterar status do usuário
   * @return array
   */
  public function ToggleUserStatus()
  {
    $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    $this->CheckSession();

    $id_user = $this->getQuery("id");
    $status  = $this->getQuery("status");

    (new \App\Classes\UsersClass)->ToggleUserStatus($id_user, $status);

    $this->masterMysqli->commit();
  }
}
