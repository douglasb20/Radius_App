<?php

use \Core\Router;

Router::group('/operators', function () {
  Router::put('/update_password', "OperatorsController@UpdatePassword")->name('update-op-password');

  Router::post('/', "OperatorsController@ListOperators")->name('list-operators');
  Router::get('/{id:[0-9]}', "OperatorsController@GetOperator")->name("get-operator");

  Router::post('/add_operator', "OperatorsController@AddOperator")->name('add-operator');

  Router::get('/reset_password_operator/{id}', "OperatorsController@RequestReset")->name('request-op-reset');
  Router::put('/update_operator', "OperatorsController@UpdateOperator")->name('update-operator');
  Router::put('/update_status_operator/{id}/{status}', "OperatorsController@ToggleOperatorStatus")->name('update-op-status');

});
