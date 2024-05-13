<?php
date_default_timezone_set("America/Sao_Paulo");
require_once __DIR__ . '/vendor/autoload.php';

$log = shell_exec("docker logs -n 50 radius-server");
printar($log);
