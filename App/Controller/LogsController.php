<?php

namespace App\Controller;

use App\Classes\LogsClass;

class LogsController extends Controller{

  public function Index()
  {
    $this->CheckSession(true);

    $data = (new LogsClass)->GetInitialData();

    $this->setTituloPagina("Logs do sistema");
    $this->setClassDivContainer("container");

    $this->render("Logs", $data);
  }

  /**
  * Função para listar log
  * @return void
  */
  public function ListarLogs(){
    $fields = $this->getPost();
    $logs = (new LogsClass)->ListarLogs($fields);
    $this->data = $logs;
  }
}

?>