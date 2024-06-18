<?php
namespace vendor\easyFrameWork\Core;
use vendor\easyFrameWork\Core\Master\EasyGlobal;
use vendor\easyFrameWork\Core\Master\SessionManager;
    abstract class Main{
        public static function helloWord(){
            echo "Hello Word";
        }
        public static function Redirect(){
            $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";  
            $CurPageURL = $protocol . $_SERVER['HTTP_HOST'] . explode("?",$_SERVER['REQUEST_URI'])[0];  
            return $CurPageURL;
        }
        public static function generatePassWord(){
            $alpha="ABDCEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+=-";
            $mdp="";
            for($i=0;$i<8;$i++){
                $mdp.=$alpha[mt_rand(0,strlen($alpha)-1)];
            }
            return $mdp;
        }
        public static function redirectWithAlert($template, $message)
        {
            $sessionManager = EasyGlobal::createSessionManager();
            $s = $sessionManager->get("streamer", SessionManager::PUBLIC_CONTEXT);
            $template->getRessourceManager()->addDirectJs("$(function(){
                    _alert('$message',function(){
                        window.location.href=\"BO-FAQ_$s\";
                    });
                });");
        }
    }
