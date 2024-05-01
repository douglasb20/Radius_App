<?php

global $_DB_MYSQLI;
date_default_timezone_set("America/Sao_Paulo");

$envFile = ".env";
session_start();

$dotenv = Dotenv\Dotenv::createImmutable( realpath( __DIR__ . '/../'),$envFile);
$dotenv->load();

$_DB_MYSQLI = (new Db)->getMysqli();

?>