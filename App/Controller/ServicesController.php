<?php

namespace App\Controller;

class ServicesController extends Controller{
  public \App\Model\EmpTipoAutenticacaoDAO $EmpTipoAutenticacaoDAO;
  public function ListarTipoAutenticacao(){
    $tipoAutenticacao = $this->EmpTipoAutenticacaoDAO->getAll();
    $this->data = $tipoAutenticacao;
  }
}

?>