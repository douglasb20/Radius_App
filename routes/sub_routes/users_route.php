<?php

use \Core\Router;

Router::group('/users', function () {

  Router::post('/', "UsersController@ListUsers")->name('list-users');
  Router::get('/{id:[0-9]}', "UsersController@getUser");
  // Router::get('/devChangePass', "UsersController@devChangePass");
  Router::post("/request_recover/{id_user}", "UsersController@RequestRecover")->name("request-recover");

  // Router::post('/', "UsersController@getUserList");
  Router::post('/add_user', "UsersController@AdicionarUsuario")->name('add-user');

  // Router::put('/reset_password_user', "UsersController@ResetSenha");
  Router::put('/update_user', "UsersController@AtualizaUsuario")->name('update-user');
  // Router::put('/update_status_user', "UsersController@AtualizaStatusUsuario");

});
