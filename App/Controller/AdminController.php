<?php

namespace App\Controller;

use App\Classes\AdminClass;

class AdminController extends Controller
{
  public \App\Model\BancoArquivosDAO $BancoArquivosDAO;
  public \App\Model\EmpresaConfigDAO $EmpresaConfigDAO;

  public function UploadImage()
  {
    $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    $id_emp = $this->getQuery("id_emp");
    (new AdminClass)->UploadImage($this->getPost(), $id_emp);

    $this->masterMysqli->commit();
  }

  public function ListarImagens()
  {
    $id_emp = $this->getQuery("id_emp");
    $where = " id_emp = {$id_emp} AND status=1 ";

    $files = $this->BancoArquivosDAO->getAll($where);

    $this->data = $files;
  }

  public function InativaImagem()
  {
    $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    $id = $this->getQuery("id");
    $id_emp = $this->getQuery("id_emp");

    $this->BancoArquivosDAO->update(["status" => "0"], " id = {$id} AND id_emp = {$id_emp}");

    // $this->setContole("Inativou uma imagem ID: {$inputJSON['id']} - Nome: {$imagem['descricao_arquivo']}");

    $this->masterMysqli->commit();
  }

  public function AtualizaConfigs()
  {
    $this->masterMysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
    $inputJSON = $this->getPut();
    $id_emp = $this->getQuery("id_emp");
    (new AdminClass)->AtualizaConfigs($inputJSON, $id_emp);
    $this->masterMysqli->commit();
  }

  /**
   * Função para gerar o token
   * @author Douglas A. Silva
   * @return string
   */
  public function GenerateToken()
  {
    $id_emp = $this->getQuery("id_emp");
    $token = $this->randomString(24);
    $this->EmpresaConfigDAO->update(['emp_token' => $token], "id_emp = {$id_emp}");

    $this->data = ["token" => $token];
  }
}
