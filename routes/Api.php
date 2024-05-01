<?php
use \Core\Router;

Router::post('/auth',"AuthController@Authentication", false);
Router::get('/main/{id_emp}', "DashboardController@Principal");


include_once("sub_routes/auth_route.php");
// include_once("sub_routes/commom_route.php");
// include_once("sub_routes/admin_route.php");
include_once("sub_routes/users_route.php");
// include_once("sub_routes/nas_route.php");
// include_once("sub_routes/services_route.php");






