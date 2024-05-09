<?php

namespace App\Model;

class LogsDAO extends \Core\Defaults\DefaultModel
{
  public $tabela = 'logs';

  /**
   * Função para retornas lista de logs
   * @author Douglas A. Silva
   */
  public function ListarLogs($where)
  {
    try {
      $query = "  SELECT l.*, op.name as operator_name, op.id as operator_id 
                  FROM {$this->tabela} as l
                  INNER JOIN operators as op
                  ON op.id = l.operator_id
            ";
      $query .= " WHERE {$where}";
      return $this->executeQuery($query);
    } catch (\Exception $e) {
      throw $e;
    }
  }
}
