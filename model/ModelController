<?php
namespace vendor\easyFrameWork\Core\Master\Controller;
use vendor\easyFrameWork\Core\Master\Controller;
use vendor\easyFrameWork\Core\Master\ResourceManager;
use vendor\easyFrameWork\Core\Master\EasyTemplate;

    class HomeController extends Controller{
        public function __construct(){
            $this->setData("TITLE","Test");
        }
        public function handleRequest()
        {  
            $config=parse_ini_file("include/config.ini",true)["localhost"];
            $template = new EasyTemplate($config,new ResourceManager());
            $template->addStylesheet("_css/index.css");
            // Définir les variables à utiliser dans le template
            $template->setVariables($this->getData());
            $data=[];
            $template->remplaceTemplate("content","index.tpl");
            $template->setLoop("testBoucle",[[
                "value"=>"100 €"
            ]]);
            // Rendre le template
            $template->render();
            
        }
      
    }