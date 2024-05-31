<?php

namespace App\Controller;

use App\Classes\OperatorsClass;
use App\Exceptions\CommomException;

class OperatorsController extends Controller
{
  public \App\Model\OperatorsDAO $OperatorsDAO;

  public function Index()
  {
    $this->CheckSession(true);

    $this->setTituloPagina("Lista de operadores");
    $this->setClassDivContainer("container");

    $this->render("Operators");
  }

  public function ListOperators()
  {
    $this->CheckSession();
    $input = $this->getPost();
    $operators = (new OperatorsClass)->ListOperators($input);
    $this->data = $operators;
  }

  
  /**
   * Função para pegar usuário
   */
  public function GetOperator()
  {
    $this->CheckSession();
    $id = $this->getQuery('id');
    $operator = (new OperatorsClass)->GetOperator($id);
    $this->data = $operator;
  }

  /**
  * Função para atualizar a senha do operator
  * @return void
  */
  public function UpdatePassword(): void{
    $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    $this->CheckSession();
    $fields = $this->getPut();
    extract($fields);

    $bindPass = [
      'password' => password_hash($password, PASSWORD_BCRYPT),
    ];

    $this->OperatorsDAO->update($bindPass, " id = " . GetSessao('id_usuario') );
    $this->setControle("Alterou a própria senha");

    $this->masterMysqli->commit();
  }

  
  /**
   * Função para adicionar novo operador
   * @author Douglas A. Silva
   */
  public function AddOperator()
  {
    $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    $this->CheckSession();
    $fields = $this->getPost();
    (new OperatorsClass)->AddOperator($fields);
    $this->masterMysqli->commit();
  }
  
  /**
   * Função para adicionar novo operador
   * @author Douglas A. Silva
   */
  public function UpdateOperator()
  {
    $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    $this->CheckSession();
    $fields = $this->getPut();
    (new OperatorsClass)->UpdateOperator($fields);
    $this->masterMysqli->commit();
  }

  public function ToggleOperatorStatus()
  {
    $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    $this->CheckSession();

    $id_operator = $this->getQuery("id");
    $status  = $this->getQuery("status");

    (new OperatorsClass)->ToggleOperatorStatus($id_operator, $status);

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
    $id_operator = $this->getQuery("id");
    $operator = $this->OperatorsDAO->getOne(" id = {$id_operator} ");
    (new OperatorsClass)->RequestPassword($operator['email'], false);

    $this->masterMysqli->commit();
  }

}
