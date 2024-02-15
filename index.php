<?php
use vendor\easyFrameWork\Core\Master\Cryptographer;
require_once ("vendor/easyFrameWork/Core/Master/EasyFrameWork.php");
use vendor\easyFrameWork\Core\Master\EasyFrameWork;

use vendor\easyFrameWork\Core\Master\Router;

use vendor\easyFrameWork\Core\Master\Autoloader;

//use Core\Master\Controller\HomeController;
EasyFrameWork::INIT();
EasyFrameWork::registerClass("Cryptographer",new Cryptographer());
//require_once "vendor/easyFrameWork/Core/Master/autoload.class.php";
//use Main\Main;
Autoloader::register();
$router = new Router();
$router->addRoute('', 'HomeController');
$router->route($_SERVER["REQUEST_URI"],["content"=>"Ici le contenu de la page"]);
