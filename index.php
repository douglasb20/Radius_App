<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT,DELETE');

$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    header("Access-Control-Allow-Headers: *");
    die();   
}

require_once __DIR__ . '/vendor/autoload.php';

// Qualquer erro (fatal,warning,deprecated) , vai ser retornado uma exception 
set_error_handler('handler');


new Core\Index;