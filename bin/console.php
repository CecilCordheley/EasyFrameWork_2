#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/easyFrameWork/Core/Master/Autoloader.php';

use Commandes\GeneratePage;
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\EnvParser;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\Autoloader;
use vendor\easyFrameWork\Core\Master\SqlEntities;
require __DIR__ . '/../src/Commandes/CrypterCommande.php';
require __DIR__ . '/../src/Commandes/GenerateEntities.php';
require __DIR__."/../src/Commandes/GeneratePage.php";
use Commandes\CrypterCommande;
use Commandes\GenerateEntities;
EasyFrameWork::INIT("./../vendor/easyFrameWork/Core/config/config.json");
Autoloader::register();
$commande = $argv[1] ?? null;
$argument = $argv[2] ?? null;
// Vérification de la commande et exécution
$commands=[
    ["name"=>"generatePage","desc"=>"Generate page with controller"],
    ["name"=>"generateEntities","desc"=>"Generate SQLEntities from table","param"=>["tableName"]],
    ["name"=>"HashCrypt","desc"=>"Encrypt and Hash a string with the MD2 algorithm","param"=>["textToEncrypt"]],
    ["name"=>"crypt","desc"=>"Encrypt a string","param"=>["textToEncrypt"]],
    ["name"=>"decrypt","desc"=>"Decrypt a string","param"=>["textToDecrypt"]],
    [
        "name"=>"generatePassWord",
        "desc"=>"Create a random password by the specific method for user Instance",
        "param"=>["size"]
    ]
];
switch ($commande) {
    case "generatePage":{
        $pageName=readline("Page Name : ");
        $pageGenerator=new GeneratePage($pageName);
        $pageGenerator();
        break;
    }
    case "allCommands":{
        echo "all commands available\n---------\n";
        array_walk($commands,function($el){
            echo $el["name"];
            echo "\n {$el["desc"]}\n parameters:";
            foreach($el["param"] as $p){
                echo "\n\t".$p;
            }
            echo "\n-------\n";
        });
        echo "\nEnds of commands";
        break;
    }
    case "generatePassWord":{
        $crypto=new Cryptographer();
        $size=$argument;
        $mdp="";
        $alpha="ACDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz0123456789@^";
        for($i=0;$i<$size;$i++){
            $index=rand(0,strlen($alpha));
            $mdp.=$alpha[$index];
        }
        echo "clean password : $mdp";
        $crypt=$crypto->hashString($mdp);
        echo "\n------------\n";
        echo "crypt password : $crypt";
        break;
    }
    case "generateEntities": {
        $env=new EnvParser(EasyFrameWork::$Racines["dirAccess"]."/.env");
        $base=$env->get("BDD");
            $gen = new GenerateEntities();
            if ($argument != null)
                $gen->handle($argument);
            else{
                echo "Génération de toute les table de la base {$base}";
                
            }
            //  echo "test".PHP_EOL;
            break;
        }
        case 'HashCrypt':
            $crypter = new CrypterCommande();
            echo $crypter($argument) . PHP_EOL;
            break;
    case 'crypt':
        $crypter = new CrypterCommande();
        echo $crypter($argument,1) . PHP_EOL;
        break;
    case 'decrypt':
            $crypter = new CrypterCommande();
            echo $crypter($argument,2) . PHP_EOL;
            break;
    default:
        echo "Commande inconnue" . PHP_EOL;
        break;
}
