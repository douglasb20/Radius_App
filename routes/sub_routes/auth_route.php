<?php

//  Rota que não precisa de id_emp;

use \Core\Router;
Router::group('/auth', function(){
  Router::post("/login_auth_request", "AuthController@AuthLogin",false)->name("login-auth");
  Router::get("/password_forgot_request/{user_email:[\W|\w]}", "AuthController@ForgotPassword")->name("forgot-password");
  
});
