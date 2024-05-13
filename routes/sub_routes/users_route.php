<?php

use \Core\Router;

Router::group('/users', function () {
  
  Router::get('/{id:[0-9]}', "UsersController@GetUser")->name("get-user");
  Router::get('/reset_password_user/{id_user}', "UsersController@RequestReset")->name('request-reset');
  Router::get('/log_user/{id}', "UsersController@LogUser")->name('log-user');
  
  Router::post('/', "UsersController@ListUsers")->name('list-users');
  Router::post('/add_user', "UsersController@AdicionarUsuario")->name('add-user');

  Router::put('/update_user', "UsersController@AtualizaUsuario")->name('update-user');
  Router::put('/update_status_user/{id}/{status}', "UsersController@ToggleUserStatus")->name('update-status');

});
