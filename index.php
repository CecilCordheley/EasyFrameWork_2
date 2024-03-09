<?php
use vendor\easyFrameWork\Core\Master\Cryptographer;
require_once ("vendor/easyFrameWork/Core/Master/EasyFrameWork.php");
use vendor\easyFrameWork\Core\Master\EasyFrameWork;

use vendor\easyFrameWork\Core\Master\Router;

use vendor\easyFrameWork\Core\Master\Autoloader;    
use vendor\easyFrameWork\Core\Master\EasyGlobal;
//use Core\Master\Controller\HomeController;
EasyFrameWork::INIT();
EasyFrameWork::registerClass("Cryptographer",new Cryptographer());
$sessionManager=EasyGlobal::createSessionManager();
$sessionManager->set("test","Ici ma variable de session");
//require_once "vendor/easyFrameWork/Core/Master/autoload.class.php";
//use Main\Main;

$router = new Router();
$router->addRoute('', 'HomeController');
$router->route($_SERVER["REQUEST_URI"],["content"=>"Ici le contenu de la page"]);
