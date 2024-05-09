<?php

namespace App\Classes;

class LogsClass extends \Core\Defaults\DefaultClassController
{
  public \App\Model\LogsDAO $LogsDAO;
  public \App\Model\OperatorsDAO $OperatorsDAO;

  public function GetInitialData()
  {
    $operatorsList = $this->OperatorsDAO->SelectOperators();

    $data = [
      "operatorsList" => $operatorsList
    ];

    return $data;
  }

  /**
   * Lista de logs
   */
  public function ListarLogs($fields)
  {
    extract($fields);
    $where = "1=1";

    if (is_array($operators)) {
      $operators = implode("','", $operators);
      $where .= " AND op.id in ({$operators})";
    } else {
      if ($operators !== "-1") {
        $where .= " AND op.id = {$operators}";
      }
    }
    
    $data_ate = \DateTime::createFromFormat('d/m/Y', $data_ate)->format('Y-m-d');
    $where .= " AND DATE(l.event_date) <= '$data_ate' ";
    
    if(!empty($data_de)){
      $data_de  = \DateTime::createFromFormat('d/m/Y', $data_de)->format('Y-m-d');
  
      $where .= " AND DATE(l.event_date) BETWEEN '$data_de' AND '$data_ate' ";
    }

    $logs = $this->LogsDAO->ListarLogs($where);
    return $logs;

  }
}
