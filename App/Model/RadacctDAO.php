<?php

namespace App\Model;

class RadacctDAO extends \Core\Defaults\DefaultModel
{
  public $tabela = 'radacct';

  public function RelacaoUso($username)
  {
    try {

      $query = "SELECT *, sec_to_time(radacct.acctsessiontime) AS duracao FROM radacct ";
      $query .= "WHERE username = '{$username}' ORDER BY radacct.radacctid desc";

      $response = $this->executeQuery($query);

      return $response;
    } catch (\Exception $e) {
      throw $e;
    }
  }
}
