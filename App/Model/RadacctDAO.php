<?php

namespace App\Model;

class RadacctDAO extends \Core\Defaults\DefaultModel
{
  public $tabela = 'radacct';

  public function qdtCliOn($id_emp)
  {
    try {
      $query = "SELECT count(*) as qtdClientesOn FROM {$this->tabela} WHERE acctstoptime is null AND calledstationid != ''  AND id_emp = {$id_emp}";

      return $this->executeQuery($query);
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function os5MaisAcessados($id_emp)
  {
    try {
      $query = "  SELECT 
                      count(r.username) AS qtd, 
                      r.username, 
                      sum(r.acctinputoctets) AS upload, 
                      sum(r.acctoutputoctets) AS down, 
                      c.usu_gender FROM radacct as r
                  INNER JOIN cliusu as c
                      ON c.usu_email = r.username 
                  where r.servicetype='' AND r.id_emp = {$id_emp}
                  group by r.username 
                  order by qtd desc limit 5";

      return $this->executeQuery($query);
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function osUltimosAcessos($id_emp)
  {
    try {
      $query = "  SELECT 
                      r.username, 
                      r.nasipaddress, 
                      r.acctstarttime, 
                      n.description, 
                      cli.usu_gender 
                  FROM radacct AS r
                  INNER JOIN nas as n
                      ON r.nasipaddress = n.nasname 
                  INNER JOIN cliusu AS cli 
                      ON cli.usu_email = r.username 
                  WHERE r.servicetype = '' AND r.acctstarttime < NOW() AND r.id_emp = {$id_emp}
                  ORDER BY r.acctstarttime desc 
                  LIMIT 5";

      return $this->executeQuery($query);
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function relacaoUso($id, $id_emp)
  {
    try {

      $query = "SELECT radacct.*, sec_to_time(radacct.acctsessiontime) AS duracao FROM radacct ";
      $query .= "INNER JOIN cliusu ON (radacct.username = cliusu.usu_email or radacct.username = cliusu.usu_cpf ) ";
      $query .= "WHERE cliusu.id ='{$id}' AND radacct.id_emp = {$id_emp} ORDER BY radacct.radacctid desc";

      $response = $this->executeQuery($query);

      return $response;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function ListarClientesConectados($id_emp)
  {
    try {

      $query = "  SELECT 
                      ra.radacctid, 
                      ra.username, 
                      ra.nasipaddress, 
                      ra.acctstarttime, 
                      sec_to_time(ra.acctsessiontime) AS session_time, 
                      nas.shortname ,
                  FROM radacct AS ra 
                  LEFT JOIN nas ON nas.nasname = ra.nasipaddress 
                  WHERE ra.acctstoptime is null AND ra.id_emp = {$id_emp}
              ";

      $response = $this->executeQuery($query);

      return $response;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function UserSimul($username, $id_emp)
  {
    try {
      $query = "SELECT username 
                FROM {$this->tabela} 
                WHERE 
                  acctstoptime IS NULL 
                AND (servicetype != 'Login-User' OR servicetype IS NULL) 
                AND username = '{$username}'  
                AND id_emp = {$id_emp}";

      return $this->executeQuery($query);
    } catch (\Exception $e) {
      throw $e;
    }
  }
}
