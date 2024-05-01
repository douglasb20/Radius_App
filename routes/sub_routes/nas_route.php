<?php
use \Core\Router;

Router::group("/{id_emp:[0-9]}/nas",function(){
    Router::get("/{status:-?[0-9]}","NasController@ListarNas");

    Router::post("/add_nas", "NasController@AdicionarNas");
    
    Router::put("/update_nas", "NasController@AtualizarNas");
    Router::put("/update_nas_status/{id_nas:[0-9}/{status:-?[0-9]}", "NasController@AtualizarNasStatus");
});