<?php

namespace vendor\easyFrameWork\Core\Master;

class Router
{
    private $routes = [];
    private $data = [];
    public function __construct($ctrlDirectory = "./_ctrl/")
    {
        foreach (scandir($ctrlDirectory) as $file) {

            if ($file != "." && $file != "..") {
                require_once $ctrlDirectory . $file;
                //  echo $ctrlDirectory.$file;
            }
        }
    }
    public function addRoute($path, $callback)
    {
        $this->routes[$path] = EasyFrameWork::$Racines["controller"] . $callback;
        //  var_dump($this->routes);
    }
    public function setData($key, $value)
    {
        $this->data[$key] = $value;
    }
    public function route($requestUri, $data = [])
    {
        $arr = explode("/", $requestUri);
      //  var_dump($arr);
        if (count($arr) > 1) {
            $uri = end($arr);
        } else {
            $uri = str_replace("/","",$requestUri);
        }
       // echo $uri;
        foreach ($this->routes as $path => $controller) {
            //   $classe=get_declared_classes();
            // sort($classe);
            //echo ("POP".$uri ."=". $path);
            if ($uri === $path) {
             //   echo ($controller);
                // Vérifier si le contrôleur existe
                if (class_exists($controller)) {
                   // echo "classe exist";
                    // Créer une instance du contrôleur et appeler handleRequest()
                    $controllerInstance = new $controller();
               //     var_dump($controllerInstance);
                    if (!empty($data)) {
                        foreach ($data as $key => $value)
                            $controllerInstance->setData($key, $value);
                    }
                    $controllerInstance->handleRequest();
                    return;
                }
            }
        }
        $this->notFound();
    }

    private function notFound()
    {
        echo "Page non trouvée";
    }
}
