<?php

namespace Core\Defaults;

use mysqli;

abstract class DefaultClassController
{
  public ?mysqli $masterMysqli;
  public \App\Model\LogDAO $LogDAO;

  function __construct()
  {
    try {

      $this->masterMysqli = $GLOBALS['_DB_MYSQLI'];
      $this->iniciaModels();
    } catch (\Exception $e) {
      throw $e;
    }
  }


  /**
   * Inicia todos os models. Define spl_autoload nas Models e api's
   * 
   * @access private
   * @link construct
   */
  private function iniciaModels()
  {
    try {

      // Inicia Models / Daos
      $all_model = array_slice(scandir(ROOT_PATH . '/' . MODEL_PATH), 2);
      foreach ($all_model as $model) {
        $model              = ucfirst($model);
        $model              = str_replace('.php', '', $model);
        $modelWithNamespace = 'App\\Model\\' . $model;

        $this->$model = new $modelWithNamespace($this->masterMysqli);
      }
      spl_autoload_register(array($this, "pathsToAutoload"));
    } catch (\Exception $e) {
      throw $e;
    }
  }
  /**
   * Aqui ficam os caminhos para o spl_autoload_register puxar automaticamente os daos e classes e apis (e subdiretorios).
   * 
   * @access protected 
   */
  protected function pathsToAutoload()
  {
    try {
      $models = glob(ROOT_PATH . '/' . MODEL_PATH . '/*');
      foreach ($models as  $model) {
        include_once $model;
      }
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * Aqui ficam os caminhos para o spl_autoload_register puxar automaticamente os daos e classes e apis (e subdiretorios).
   * 
   * @access protected 
   * @param qualquerClass (string): classe/api a ser importada
   */
  protected function pathsToAutoload2($qualquerClass)
  {
    try {
      $paths = [
        __DIR__ . '/../class/' . $qualquerClass . ".php",
        __DIR__ . '/../api/' . $qualquerClass . ".php",
      ];
      //subpastas de classe
      $all_class = array_slice(scandir(__DIR__ . '/../class/'), 2);
      foreach ($all_class as $class) {
        if (is_dir(__DIR__ . '/../class/' . $class)) {
          $paths[] = __DIR__ . '/../class/' . $class . '/' . $qualquerClass . ".php";
        }
      }
      //subpastas de classe
      $all_api = array_slice(scandir(__DIR__ . '/../api/'), 2);
      foreach ($all_api as $api) {
        if (is_dir(__DIR__ . '/../api/' . $api)) {
          $paths[] = __DIR__ . '/../api/' . $api . '/' . $qualquerClass . ".php";
        }
      }
      foreach ($paths as $path) {
        if (file_exists($path)) {
          include_once $path;
        }
      }
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function setContole($msg = "")
  {
    try {
      $bindControle = [
        "user_id" => GetSessao('id_usuario'),
        "description" => $msg
      ];

      $this->LogDAO->insert($bindControle);
    } catch (\Exception $e) {
      throw $e;
    }
  }
}
