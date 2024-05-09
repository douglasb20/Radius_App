<?php
use \Core\Router;

Router::group('/logs',function(){
  Router::post("/","LogsController@ListarLogs")->name('list-logs');
});