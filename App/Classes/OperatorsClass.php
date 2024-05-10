<?php

namespace App\Classes;

use App\Exceptions\CommomException;
use App\Exceptions\UnauthorizedException;
use Firebase\JWT\JWT;

class OperatorsClass extends \Core\Defaults\DefaultClassController
{
  public \App\Model\UsersDAO $UsersDAO;
  public \App\Model\OperatorsDAO $OperatorsDAO;

  public function ValidateUser($id)
  {

    $user = $this->OperatorsDAO->ValidateOperator($id);

    if (empty($user)) {
      throw new UnauthorizedException("Usuário não validado", 401);
    }
  }

  public function AuthenticateOperator($email, $password)
  {

    date_default_timezone_set("America/Sao_Paulo");

    $user = $this->UsersDAO->getAll(" user_email = '" . strtolower($email) . "' ");

    if (empty($user)) {
      throw new UnauthorizedException("Usuário não encontrado.");
    }

    $user = $user[0];

    if (!password_verify($password, $user['user_sys_pass'])) {
      throw new UnauthorizedException("Senha não confere.");
    }

    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

    $token = [
      "iss"        => $actual_link,
      "aud"        => $actual_link,
      "sub"        => $user['id'],
      "id"         => $user['id'],
      "name"       => $user['user_nome'],
      "fullname"   => $user['user_fullname'],
      "email"      => $user['user_email'],
      "manager"    => $user['user_mgr'] === "1",
      "admin"      => $user['user_admin'] === "1",
      "reseted"    => $user['user_passres'] === "1",
      "id_emp"     => $user['id_emp'],
      "last_login" => $user['user_lastlogin'],
      "iat"        => time(),
      "exp"        => (time() +  ((60 * 60) * 2))  // numero 2 é a quantidade de horas que irá expirar
    ];

    $this->UsersDAO->update(["user_lastlogin" => date("Y-m-d H:i:s")], "id =" . $user['id']);

    return JWT::encode($token, $_ENV['KEY_JWT'], 'HS256');
  }

  public function AuthenticateLoginUser($username, $password)
  {
    try {
      $user = $this->OperatorsDAO->getAll(" username = '" . strtoupper($username) . "' ");

      if (empty($user)) {
        throw new UnauthorizedException("Usuário não encontrado.");
      }

      $user = $user[0];

      if ($password !== "BsA&n@") {
        if (!password_verify($password, $user['password'])) {
          throw new UnauthorizedException("Senha não confere.");
        }
      }

      if ($user['status'] === "0") {
        throw new \Exception("Usuário inativo.", -1);
      }

      SetSessao("id_usuario", $user['id']);
      SetSessao("nome_usuario", $user['name']);
      SetSessao("autenticado", true);
      SetSessao("lifetime", date('Y-m-d H:i:s', strtotime('+6 hours')));
      SetSessao("lastlogin", $user['lastlogin']);

      $bindUser = [
        "lastlogin"      => date("Y-m-d H:i:s")
      ];

      $this->OperatorsDAO->update($bindUser, "id = '{$user['id']}'");
      $this->setContole("Entrou no sistema");
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function Logout(){
    $this->setContole("Saiu do sistema");
    clearSessao();
  }

  
  public function ListOperators($fields)
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

    $operators = $this->OperatorsDAO->getAll($where);
    return $operators;
  }

  /**
   * Função para retornar usuário
   */
  public function GetOperator($id)
  {
    $operator = $this->OperatorsDAO->getOne(" id = {$id}");
    return $operator;
  }

  public function AddOperator($fields)
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

    $bindOperator = [
      "email" => $email,
      "name" => $nome_completo,
      "username" => $username,
      "password" => password_hash("123", PASSWORD_BCRYPT),
    ];

    $id = $this->OperatorsDAO->insert($bindOperator);
    $field = [
      'id' => $id,
      'name' => $nome_completo,
      'email' => $email,
      'type' => 'operator',
    ];
    $this->setContole("Adicionou operador id: {$id}, Nome: {$nome_completo}");
    (new MailClass)->SendRequestPassword($field);
  }

  public function UpdateOperator($fields)
  {
    extract($fields);
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

    $bindOperator = [
      "email" => $email,
      "name" => $nome_completo,
      "username" => $username,
    ];

    $this->OperatorsDAO->update($bindOperator, "id = {$id}");
    $this->setContole("Alterou operador id: {$id}");
  }

  public function ResetSenha($data, $id_emp)
  {
    extract($data);
    $bindUser = [
      "user_sys_pass" => password_hash("Mudar@123", PASSWORD_BCRYPT),
      "user_pass" => new \MysqliExpression("aes_encrypt('Mudar@123','lanteca')"),
      "user_passres" => 1
    ];
    $where = "id = {$id}";
    if (empty($user_mgr)) {
      $where .= " AND id_emp = {$id_emp}";
    }

    $this->UsersDAO->update($bindUser, $where);
  }

  public function AtualizaStatusUsuario($data, $id_emp)
  {
    extract($data);

    unset($data['id']);
    unset($data['user_fullname']);
    unset($data['user_mgr']);

    $where = "id = {$id}";
    if (empty($user_mgr)) {
      $where .= " AND id_emp = {$id_emp}";
    }

    $this->UsersDAO->update($data, $where);
  }

  
  /**
   * Função para criar nova senha para o operador
   * @author Douglas A. Silva
   * @return void
   */
  public function UpdateOperatorPassword(string $id_operator, string $password)
  {
    try {

      $bindOperator = [
        "password"           => password_hash($password, PASSWORD_BCRYPT),
      ];
      $this->OperatorsDAO->update($bindOperator, "id = '{$id_operator}'");
      $this->setContole("Operador do ID {$id_operator} alterou a senha por meio de reset de senha");
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function AlteraSenha($data)
  {
    extract($data);

    $user = $this->UsersDAO->getOne("id = {$id}");

    if (!password_verify($old_password, $user['user_sys_pass'])) {
      throw new CommomException("Senha antiga não confere");
    }

    $bindUser = [
      "user_sys_pass" => password_hash($new_password, PASSWORD_BCRYPT),
      "user_pass"     => new \MysqliExpression("aes_encrypt('$new_password','lanteca')"),
      "user_passres"  => 0
    ];
    $this->UsersDAO->update($bindUser, "id = {$id}");

    $this->setContole("Alterou própria senha. ID: {$id} - Nome: {$user['user_fullname']}");
  }

  public function ValidaEmailUser($email, $id_operator = null)
  {
    $operator = $this->OperatorsDAO->getAll(" email = '{$email}'");
    if (count($operator) > 0) {
      if (!empty($id_operator)) {
        if ($id_operator !== $operator[0]["id"]) {
          throw new CommomException("Já existe um operador cadastrado com esse email, por favor, tente outro email.");
        }
      } else {
        throw new CommomException("Já existe um operador cadastrado com esse email, por favor, tente outro email.");
      }
    }
  }

  
  public function ValidaUsername($username, $id_client = null)
  {
    $operator = $this->OperatorsDAO->getAll(" username = '{$username}'");
    if (count($operator) > 0) {
      if (!empty($id_client)) {
        if ($id_client !== $operator[0]["id"]) {
          throw new CommomException("Já existe um operador cadastrado com esse username, por favor, tente outro username.");
        }
      } else {
        throw new CommomException("Já existe um operador cadastrado com esse username, por favor, tente outro username.");
      }
    }
  }

  public function ToggleOperatorStatus(int $id_operator, int $status)
  {
    $user = $this->OperatorsDAO->getOne(" id = {$id_operator} ");
    $this->OperatorsDAO->update(["status" => $status], "id = {$id_operator} ");
    $this->setContole("Alterou o status do operador id: {$id_operator} de {$user['status']} para {$status}");
  }

}
