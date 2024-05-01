<?php
use \Core\Router;

Router::group('/services',function(){
    
    Router::get('/auth_type', "ServicesController@ListarTipoAutenticacao");
    Router::get("/companies", "EmpresaController@Listar");
});