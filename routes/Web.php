<?php

use Core\Router;

// Router::get("/","HomeController@index", false);
Router::get("/login","AuthController@Index",false)->name("login-page");
Router::get("/recover_password/{forgot_token:[\W|\w]}", "UsersController@RecoverPassword")->name("recover-password");

Router::get("/","UsersController@Index",false)->name("home");
Router::get("/nas","NasController@Index",false)->name("nas");


?>
