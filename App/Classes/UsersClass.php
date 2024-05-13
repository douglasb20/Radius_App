<?php

namespace App\Classes;

use App\Exceptions\CommomException;
use App\Services\PhpMailerService;

class UsersClass extends \Core\Defaults\DefaultClassController
{
  public \App\Model\UsersDAO $UsersDAO;
  public \App\Model\RadacctDAO $RadacctDAO;

  public function ListUsers($fields)
  {
    extract($fields);
    $where = "1=1";

    if (is_array($status)) {
      $status = implode("','", $status);
      $where .= " AND status in ('{$status}')";
    } else {
      if ($status !== "-1") {
        $where .= " AND status = '{$status}'";
      }
    }

    $users = $this->UsersDAO->getAll($where);
    return $users;
  }

  /**
   * Função para retornar usuário
   */
  public function GetUser($id)
  {
    $user = $this->UsersDAO->getOne(" id = {$id}");
    return $user;
  }

  public function AdicionarUsuario($fields)
  {
    extract($fields);
    $this->ValidaEmailUser($email);
    $this->ValidaUsername($username);
    $nome_completo = "";
    $nome = ucwords(trim(mb_strtolower($name)));
    if (!empty($lastname)) {
      $sobrenome = ucwords(trim(mb_strtolower($lastname)));
      $nome_completo = "{$nome} {$sobrenome}";
    } else {
      $nome_completo = $nome;
    }

    $bindUser = [
      "username" => $username,
      "email" => $email,
      "name" => $nome_completo,
      "group" => $group,
      "custom_group" => mb_strtolower($custom_group),
    ];

    $id = $this->UsersDAO->insert($bindUser);
    $fields = [
      'id' => $id,
      'name' => $nome_completo,
      'email' => $email,
      'type' => 'user'
    ];
    $this->setContole("Adicionou usuário id: {$id}, Nome: {$nome_completo}");
    (new MailClass)->SendRequestPassword($fields);
  }

  public function AtualizaUsuario($fields)
  {
    extract($fields);
    $user = $this->UsersDAO->getAll(" id = {$id}");
    if (empty($user)) {
      throw new CommomException("Usuário não encontrado");
    }
    $user = $user[0];

    $this->ValidaEmailUser($email, $id);
    $this->ValidaUsername($username, $id);
    $nome_completo = "";
    $nome = ucwords(trim(mb_strtolower($name)));
    if (!empty($lastname)) {
      $sobrenome = ucwords(trim(mb_strtolower($lastname)));
      $nome_completo = "{$nome} {$sobrenome}";
    } else {
      $nome_completo = $nome;
    }

    $bindUser = [
      "username" => $username,
      "email" => $email,
      "name" => $nome_completo,
      "group" => $group,
      "custom_group" => mb_strtolower($custom_group),
    ];

    $this->UsersDAO->update($bindUser, " id = {$id} ");
    $this->setContole("Alterou usuário id: {$id}, Nome: {$nome_completo}");
  }

  /**
   * Função para solicitar redefinição de senha
   * @author Douglas A. Silva
   * @return void
   */
  public function RequestReset($id_user)
  {
    $user = $this->UsersDAO->getOne(" id = {$id_user} ");
    $fields = [
      'id' => $user['id'],
      'name' => $user['name'],
      'email' => $user['email'],
      'type' => 'user'
    ];
    (new MailClass)->SendRequestPassword($fields);
    $this->setContole("Enviou reset de senha do usuário id: {$user['id']}, Nome: {$user['name']}");
  }

  /**
   * Função para criar nova senha para o usuário
   * @author Douglas A. Silva
   * @return void
   */
  public function UpdateUserPassword(string $id_user, string $password)
  {
    try {

      $bindUser = [
        "password"           => $password,
        "is_request_password" => 0
      ];
      $this->UsersDAO->update($bindUser, "id = '{$id_user}'");
      $this->setContole("Usuário do ID {$id_user} alterou a senha por meio de reset de senha");
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function ValidaEmailUser($email, $id_client = null)
  {
    $user = $this->UsersDAO->getAll(" email = '{$email}'");
    if (count($user) > 0) {
      if (!empty($id_client)) {
        if ($id_client !== $user[0]["id"]) {
          throw new CommomException("Já existe um usuário cadastrado com esse email, por favor, tente outro email.");
        }
      } else {
        throw new CommomException("Já existe um usuário cadastrado com esse email, por favor, tente outro email.");
      }
    }
  }

  public function ValidaUsername($username, $id_client = null)
  {
    $user = $this->UsersDAO->getAll(" username = '{$username}'");
    if (count($user) > 0) {
      if (!empty($id_client)) {
        if ($id_client !== $user[0]["id"]) {
          throw new CommomException("Já existe um usuário cadastrado com esse username, por favor, tente outro username.");
        }
      } else {
        throw new CommomException("Já existe um usuário cadastrado com esse username, por favor, tente outro username.");
      }
    }
  }

  /**
   * Função alterar status do usuário
   * @author Douglas A. Silva
   * @param int $id_user id do usuário que irá alterar 
   * @param int $status status que será alterado
   * @return void
   */
  public function ToggleUserStatus(int $id_user, int $status)
  {
    $lStatus = ['inativo', 'ativo'];
    $user = $this->UsersDAO->getOne(" id = {$id_user} ");
    $this->UsersDAO->update(["status" => $status], "id = {$id_user} ");
    $this->setContole("Alterou o status do usuário id: {$id_user} de {$lStatus[$user['status']]} para {$lStatus[$status]}");
  }

  public function LogUser($id){
    $user = $this->UsersDAO->getOne(" id = {$id} ");
    $log = $this->RadacctDAO->RelacaoUso($user['username']);

    return $log;

  }
}
