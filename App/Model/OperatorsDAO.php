<?php

namespace App\Model;

class OperatorsDAO extends \Core\Defaults\DefaultModel
{
  public $tabela = 'operators';
  public function ValidateOperator($id)
  {
    try {

      $query = "SELECT * FROM {$this->tabela} ";
      $query .= " WHERE id = {$id} ";

      return $this->executeQuery($query);
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function SelectOperators(){
    $query = "SELECT id, name FROM {$this->tabela} WHERE status = 1";
    return $this->executeQuery($query);
  }

}
