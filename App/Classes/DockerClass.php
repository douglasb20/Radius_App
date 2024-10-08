<?php

namespace App\Classes;

class DockerClass extends \Core\Defaults\DefaultClassController
{
  public \App\Model\SystemConfigDAO $SystemConfigDAO;

  public function RestartRadius()
  {
    try {
      $cmd = $this->SystemConfigDAO->getOne("`key` = 'radius_restart'");
      if (!empty($cmd['value'])) {
        shell_exec($cmd['value']);
      }
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function GetRadiusLog()
  {
    ignore_user_abort(true);
    $cmd = $this->SystemConfigDAO->getOne(" `key` = 'radius_log' ");
    if (!empty($cmd['value'])) {

      $log = shell_exec($cmd['value']);
      return $log;
    }
  }
}
