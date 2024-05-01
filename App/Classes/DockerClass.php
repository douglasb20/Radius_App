<?php

namespace App\Classes;

class DockerClass extends \Core\Defaults\DefaultClassController{
  private \App\Model\SystemConfigDAO $SystemConfigDAO;

  public function RestartRadius(string $key){
    try{
      $cmd = $this->SystemConfigDAO->getOne("key = {$key}");
      if(!empty($cmd['value'])){
        shell_exec( $cmd['value'] );
      }
    }catch(\Exception $e){
      throw $e;
    }
  }

  public function GetRadiusLog(string $key)
  {
    $cmd = $this->SystemConfigDAO->getOne("key = {$key}");
    if(!empty($cmd['value'])){
      header('Content-Type: text/event-stream');
      header('Cache-Control: no-cache');
  
      $p = popen($cmd['value'], "r");
      while ($log = fgets($p, 2048)) {
  
        echo 'data: ' . $log . "\n\n";
  
        flush();
        if (connection_aborted()) exit();
      }
    }
  }

}