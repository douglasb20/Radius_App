<?php
define("ROOT_PATH", realpath(__DIR__ . "/../"));

define("CONTROLLER_NAMESPACE", "App\\Controller\\");
define("CONTROLLER_PATH", "App/Controller/");

define("MODEL_NAMESPACE", "App\\Model\\");
define("MODEL_PATH", "App/Model/");


if(isset($_SERVER['REQUEST_SCHEME'])){
    define("URL_BASED", 
    (
        $_SERVER['REQUEST_SCHEME'] === "https" || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === "on") 
        ? "https://"  
        : "http://"
    ) 
    . $_SERVER['HTTP_HOST'] .'/'. trim($_ENV['BASE_URL'],"/") );
    
    define("URL_ROOT", 
    (
        $_SERVER['REQUEST_SCHEME'] === "https" || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === "on") 
        ? "https://"  
        : "http://"
    ) 
    . $_SERVER['HTTP_HOST'] .'/'. trim($_ENV['CONTEXT_PATH'],"/") );
    
    define("ASSETS_IMAGES", trim(URL_ROOT,"/") . '/assets/images' );
    define("URL_IMAGES", trim(URL_ROOT,"/") . '/files/images' );
    define("URL_DOCUMENTS", trim(URL_ROOT,"/") . '/files/documents' );
}

define("PATH_ASSETS_IMAGES", ROOT_PATH . '/assets/images' );
define("PATH_IMAGES", ROOT_PATH. '/files/images' );
define("PATH_DOCUMENTS", ROOT_PATH. '/files/documents' );

?>