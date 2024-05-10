<?php

use Core\Router;

// Router::get("/","HomeController@index", false);
Router::get("/login","AuthController@Index",false)->name("login-page");
Router::get("/recover_password/{forgot_token:[\W|\w]}", "AuthController@RecoverPassword")->name("recover-password");
Router::get("/logout", "AuthController@Logout",false)->name("logout");

Router::get("/","UsersController@Index",false)->name("home");
Router::get("/nas","NasController@Index",false)->name("nas");
Router::get("/operators","OperatorsController@Index",false)->name("operators");
Router::get("/logs","LogsController@Index",false)->name("logs");


?>
