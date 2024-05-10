<?php

//  Rota que não precisa de id_emp;

use \Core\Router;
Router::group('/auth', function(){
  Router::post("/login_auth_request", "AuthController@AuthLogin",false)->name("login-auth");
  Router::get("/password_forgot_request/{email:[\W|\w]}", "AuthController@ForgotPassword")->name("forgot-password");
  Router::post("/request_recover/{id}/{type}", "AuthController@RequestRecover")->name("request-recover");
  
});
