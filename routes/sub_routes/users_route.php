<?php

use \Core\Router;

Router::group('/users', function () {

  Router::post('/', "UsersController@ListUsers")->name('list-users');
  Router::get('/{id:[0-9]}', "UsersController@GetUser")->name("get-user");

  Router::post('/add_user', "UsersController@AdicionarUsuario")->name('add-user');

  Router::get('/reset_password_user/{id_user}', "UsersController@RequestReset")->name('request-reset');
  Router::put('/update_user', "UsersController@AtualizaUsuario")->name('update-user');
  Router::put('/update_status_user/{id}/{status}', "UsersController@ToggleUserStatus")->name('update-status');

});
