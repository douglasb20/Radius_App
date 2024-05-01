<?php

//  Rota que não precisa de id_emp;

use \Core\Router;

Router::group('/commom', function () {
  Router::put('/users/update_password_user', "UsersController@AlteraSenha");
  Router::get('/users/get_user_config/{id:[0-9]}', "UsersController@ConfigUsuario");
  Router::post('/users/managers', "UsersController@GetUsersManagersList");

  Router::get("/nas/log_radius","NasController@GetRadiusLog");

});
