<?php

namespace App\Controller;

use App\Classes\OperatorsClass;
use App\Exceptions\CommomException;

class OperatorsController extends Controller
{
  public \App\Model\OperatorsDAO $OperatorsDAO;
  /**
  * Função para atualizar a senha do operator
  * @return void
  */
  public function UpdatePassword(): void{
    $this->CheckSession();
    $fields = $this->getPut();
    extract($fields);

    $bindPass = [
      'password' => password_hash($password, PASSWORD_BCRYPT),
    ];

    $this->OperatorsDAO->update($bindPass, " id = " . GetSessao('id_usuario') );
    $this->setContole("Alterou a própria senha");
  }
}
