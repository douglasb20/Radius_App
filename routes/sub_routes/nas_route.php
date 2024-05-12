<?php
use \Core\Router;

Router::group("/nas",function(){
    Router::post("/","NasController@ListarNas")->name('listar-nas');
    Router::get("/{id:[0-9]}","NasController@GetNas")->name('get-nas');

    Router::post("/add_nas", "NasController@AdicionarNas")->name('add-nas');
    
    Router::put("/update_nas", "NasController@AtualizarNas")->name('update-nas');
    Router::put("/update_nas_status/{id_nas:[0-9}/{status:-?[0-9]}", "NasController@AtualizarNasStatus")->name('change-status');
    Router::get("/restart_radius", "NasController@RestartRadius")->name('radius-restart');
});