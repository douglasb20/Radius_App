<?php

namespace App\Controller;

use App\Classes\DockerClass;
use App\Classes\NasClass;

class NasController extends Controller
{
  public \App\Model\NasDAO $NasDAO;
  public \App\Model\SystemConfigDAO $SystemConfigDAO;

  public function Index()
  {
    $this->CheckSession(true);

    $this->setTituloPagina("Lista de concentradoras");
    $this->setClassDivContainer("container");

    $this->render("Nas");
  }

  public function ListarNas()
  {
    extract($this->getPost());
    $where = "1=1";
    if ($status !== "-1") {
      $where .= " AND status = {$status}";
    }

    $nas = $this->NasDAO->getAll($where);

    $this->data = $nas;
  }

  /**
   * Função para retornar concentradora
   * @return void
   */
  public function GetNas()
  {
    $id = $this->getQuery('id');
    $nas = $this->NasDAO->getOne(" id = {$id} ");

    $this->data = $nas;
  }

  public function AtualizarNas()
  {

    $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

    $fields = $this->getPut();
    (new NasClass)->AtualizarNas($fields);

    $this->masterMysqli->commit();
  }

  public function AtualizarNasStatus()
  {
    $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

    $id_nas = $this->getQuery("id_nas");
    $status = $this->getQuery("status");
    (new NasClass)->AtualizarNasStatus($id_nas, $status);

    $this->masterMysqli->commit();
  }

  public function AdicionarNas()
  {
    $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

    $fields = $this->getPost();
    (new NasClass)->AdicionarNas($fields);

    $this->masterMysqli->commit();
  }

  /**
   * Função para restartar radius
   * @author Douglas A. Silva
   * @return string
   */
  public function RestartRadius()
  {
    (new DockerClass)->RestartRadius();
  }

  public function GetRadiusLog(){
    $this->data = (new DockerClass)->GetRadiusLog();
  }
}
