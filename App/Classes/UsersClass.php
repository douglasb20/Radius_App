<?php

namespace App\Classes;

use App\Exceptions\CommomException;
use App\Services\PhpMailerService;

class UsersClass extends \Core\Defaults\DefaultClassController
{
  public \App\Model\UsersDAO $UsersDAO;

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

    if (!empty($data_de)) {
      $data_de  = \DateTime::createFromFormat('d/m/Y', $data_de)->format('Y-m-d');
      $data_ate = \DateTime::createFromFormat('d/m/Y', $data_ate)->format('Y-m-d');
      $where .= " AND DATE(lastlogin) BETWEEN '$data_de' AND '$data_ate' ";
    }

    $users = $this->UsersDAO->getAll($where);
    return $users;
  }

  public function AdicionarUsuario($fields)
  {
    extract($fields);
    $this->ValidaEmailUser($email);
    $this->ValidaUsername($username);
    $nome_completo = "";
    $nome = ucfirst(trim(mb_strtolower($name)));
    if (!empty($lastname)) {
      $sobrenome = ucfirst(trim(mb_strtolower($lastname)));
      $nome_completo = "{$nome} {$sobrenome}";
    } else {
      $nome_completo = $nome;
    }

    $bindUser = [
      "username" => $username,
      "email" => $email,
      "name" => $nome_completo,
      "group" => $group,
      "custom_group" => $custom_group,
    ];

    $this->UsersDAO->insert($bindUser);
    $this->SendRequestPassword($email);
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
    $nome = ucfirst(trim(mb_strtolower($name)));
    if (!empty($lastname)) {
      $sobrenome = ucfirst(trim(mb_strtolower($lastname)));
      $nome_completo = "{$nome} {$sobrenome}";
    } else {
      $nome_completo = $nome;
    }

    $bindUser = [
      "username" => $username,
      "email" => $email,
      "name" => $nome_completo,
      "group" => $group,
      "custom_group" => $custom_group,
    ];

    $this->UsersDAO->update($bindUser, " id = {$id}");
  }


  /**
   * Função para processar pedido de Password forgotten
   * @author Douglas A. Silva
   * @return void
   */
  public function SendRequestPassword(string $email)
  {
    $user = $this->UsersDAO->getAll("email = '" . strtolower($email) . "'");

    if (empty($user)) {
      throw new CommomException("Email não localizado.");
    }

    $user = $user[0];

    $forgot = [
      "id"           => $user['id'],
      "expires"      => date("Y-m-d H:i:s", strtotime("+ 30 minutes"))
    ];

    $token = encrypt(json_encode($forgot));
    $url_token = trim(URL_ROOT, "/") . route()->link("recover-password") . $token;
    
    (new MailClass)->SendRecoverPass($url_token, $user['name'], $user['mail']);
  }

  /**
   * Função para ler token de request de senha
   * @author Douglas A. Silva
   */
  public function RecoverPassword($token)
  {
    $dados = [
      "status" => true,
      "msg"    => "",
      "dados"  => []
    ];

    if (empty($token)) {
      $dados = [
        ...$dados,
        "status" => false,
        "msg"    => "Token inválido."
      ];
    }

    $token     = json_decode($token, true);
    $user           = $this->UsersDAO->getOne(" id = '{$token['id']}'");

    if ($user['user_forgotpassword'] === "0") {

      $dados = [
        ...$dados,
        "status" => false,
        "msg"    => "<strong>Atenção:</strong> O token de redefinição de senha expirou.<br>
          Por favor, solicite uma nova redefinição de senha."
      ];
    }

    $tokenTime      = $token['expires'];
    $tempoAtual     = date('Y-m-d H:i:s');

    $tokenLifeTime  = strtotime($tokenTime);
    $timestampAtual = strtotime($tempoAtual);

    // if ($tokenLifeTime < $timestampAtual) {
    //   $dados = [
    //     ...$dados,
    //     "status" => false,
    //     "msg"    => "Atenção: O token de redefinição de senha expirou.<br/>
    //       Por favor, solicite uma nova redefinição de senha."
    //   ];
    // }

    $dados["dados"] = ["id" => $user['id']];

    return $dados;
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
        "password"           => md5($password),
        "is_request_password" => 0
      ];
      $this->UsersDAO->update($bindUser, "id = '{$id_user}'");
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
}
