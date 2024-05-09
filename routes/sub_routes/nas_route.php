<?php
use \Core\Router;

Router::group("/nas",function(){
    Router::post("/","NasController@ListarNas")->name('listar-nas');

    Router::post("/add_nas", "NasController@AdicionarNas")->name('add-nas');
    
    // Router::put("/update_nas", "NasController@AtualizarNas");
    // Router::put("/update_nas_status/{id_nas:[0-9}/{status:-?[0-9]}", "NasController@AtualizarNasStatus");
});