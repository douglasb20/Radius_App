<?php

$envFile = ".env";
$dotEnv = Dotenv\Dotenv::createImmutable(realpath(__DIR__), $envFile);
$dotEnv->load();
date_default_timezone_set('America/Sao_Paulo');

return
  [
    'paths' => [
      'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
      'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
      'default_migration_table' => 'phinxlog',
      'default_environment' => 'development',
      'prod' => [
        'adapter' => 'mysql',
        'host' => $_ENV['HOST'],
        'name' => $_ENV['DBNAME'],
        'user' => $_ENV['DBUSER'],
        'pass' => $_ENV['PASSWD'],
        'port' => $_ENV['PORT'],
        'charset' => 'utf8',
      ],
      'development' => [
        'adapter' => 'mysql',
        'host' => 'localhost',
        'name' => 'radius_db',
        'user' => 'root',
        'pass' => '',
        'port' => '3306',
        'charset' => 'utf8',
      ],
    ],
    "version_order" => 'creation',
  ];
