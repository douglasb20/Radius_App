<?php

namespace App\Controller;

use App\Classes\OperatorsClass;
use App\Exceptions\UnauthorizedException;

class AuthController extends Controller
{

  public \App\Model\UsersDAO $UsersDAO;
  public \App\Model\OperatorsDAO $OperatorsDAO;

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

  /**
   * Função para criar nova senha
   * @author Douglas A. Silva
   * @return void
   */
  public function RecoverPassword()
  {
    $dados = [];
    $token = decrypt($this->getQuery("forgot_token"));
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
    if ($token['type'] === "user") {
      $target = $this->UsersDAO->getOne(" id = '{$token['id']}'");
    } else {
      $target = $this->OperatorsDAO->getOne(" id = {$token['id']}");
    }


    if ($target['user_forgotpassword'] === "0" && $token['type'] === 'user') {

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

    if ($tokenLifeTime < $timestampAtual) {
      $dados = [
        ...$dados,
        "status" => false,
        "msg"    => "Atenção: O token de redefinição de senha expirou.<br/>
          Por favor, solicite uma nova redefinição de senha."
      ];
    }

    $dados["dados"] = ["id" => $target['id'], "type" => $token['type']];
    $this
      ->setTituloPagina("Recuperação de senha")
      ->setClassDivContainer("container d-flex justify-content-center align-items-start pt-5 h-100")
      ->render("Login.recoverPassword", $dados);
  }

  /**
   * Função para criar nova senha
   * @author Douglas A. Silva
   * @return void
   */
  public function RequestRecover()
  {

    $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    extract($this->getPost());
    $id = $this->getQuery("id");
    $type = $this->getQuery("type");

    if($type === 'user'){
      (new \App\Classes\UsersClass)->UpdateUserPassword($id, $password);
    }else{
      (new OperatorsClass)->UpdateOperatorPassword($id, $password);
    }

    $this->masterMysqli->commit();
  }

  /**
  * Função para solicitar recuperação de senha
  * @return void
  */
  public function ForgotPassword(){
    $email = $this->getQuery('email');
    (new OperatorsClass)->RequestPassword($email);
  }

  public function Logout()
  {
    (new OperatorsClass)->Logout();
    route()->redirect(route()->link('login-page'));
  }
}
