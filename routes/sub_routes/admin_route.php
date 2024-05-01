<?php

use \Core\Router;

Router::group('/{id_emp:[0-9]}/admin', function () {
  Router::get("/generate_token", "AdminController@GenerateToken");
  Router::post("/update_config", "AdminController@AtualizaConfigs");

  Router::group('/file_storage', function () {

    Router::get('/list_images', "AdminController@ListarImagens");
    Router::post('/upload_image', "AdminController@UploadImage");

    Router::delete('/remove_image/{id}', "AdminController@InativaImagem");
  });
});
