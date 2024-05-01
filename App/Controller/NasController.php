<?php

namespace App\Controller;

use App\Classes\NasClass;

class NasController extends Controller
{
  public \App\Model\NasDAO $NasDAO;

  public function ListarNas()
  {
    extract($this->getQuery());
    $where = " id_emp = {$id_emp}";
    if ($status !== "-1") {
      $where .= " AND status = {$status}";
    }

    $nas = $this->NasDAO->getAll($where);

    foreach ($nas as $key => $n) {
      if (!empty($n['nas_termos_filename'])) {
        $nas[$key]['nas_termos_filename'] = URL_DOCUMENTS . "/{$n['nas_termos_filename']}";
      }

      if (!empty($n['nas_politica_filename'])) {
        $nas[$key]['nas_politica_filename'] = URL_DOCUMENTS . "/{$n['nas_politica_filename']}";
      }
    }

    $this->data = $nas;
  }

  public function AtualizarNas()
  {

    $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

    $inputJSON = $this->getPut();
    $id_emp = $this->getQuery("id_emp");
    (new NasClass)->AtualizarNas($inputJSON, $id_emp);

    $this->masterMysqli->commit();
  }

  public function AtualizarNasStatus()
  {
    $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

    $id_emp = $this->getQuery("id_emp");
    $id_nas = $this->getQuery("id_nas");
    $status = $this->getQuery("status");
    (new NasClass)->AtualizarNasStatus($id_nas, $status, $id_emp);

    $this->masterMysqli->commit();
  }

  public function AdicionarNas()
  {
    $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

    $inputJSON = $this->getPost();
    $id_emp = $this->getQuery("id_emp");
    (new NasClass)->AdicionarNas($inputJSON, $id_emp);

    $this->masterMysqli->commit();
  }

  /**
   * Função para capturar logs do radius
   * @author Douglas A. Silva
   * @return string
   */
  public function GetRadiusLog()
  {
    (new \App\Classes\DockerClass)->GetRadiusLog('radius_log');
  }
}
