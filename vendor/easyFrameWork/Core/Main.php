<?php
namespace vendor\easyFrameWork\Core;
use vendor\easyFrameWork\Core\Master\EasyGlobal;
use vendor\easyFrameWork\Core\Master\EasyTemplate;
use vendor\easyFrameWork\Core\Master\SessionManager;
use DateTime;
    abstract class Main{
        public static function FormatDate($strDate){
            $str="";
            $month=["Janvier","Fevrier","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Décembre"];
            //récupérer la date sans l'heure
            $date=explode("-",explode(" ",$strDate)[0]);
            $y=$date[0];
            $m=$date[1];
            $d=$date[2];
            $str=$date[2]." ".$month[$date[1]-1]." {$date[0]}";
            return $str;
        }
        public static function Redirect(){
            $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";  
            $CurPageURL = $protocol . $_SERVER['HTTP_HOST'] . explode("?",$_SERVER['REQUEST_URI'])[0];  
            return $CurPageURL;
        }
        /**
         * Cast un Objet "serialiser" dans la classe indiquée
         * @param mixed $object
         * @param string $class
         * @return mixed
         */
        public static function fixObject ($object, $class = 'stdClass')
        {
            $ser_data = serialize($object);
            # preg_match_all('/O:\d+:"([^"]++)"/', $ser_data, $matches); // find all classes
          
            /*
             * make private and protected properties public
             *   privates  is stored as "s:14:\0class_name\0property_name")
             *   protected is stored as "s:14:\0*\0property_name")
             */
            $ser_data = preg_replace_callback('/s:\d+:"\0([^\0]+)\0([^"]+)"/',
              function($prop_match) {
                list($old, $classname, $propname) = $prop_match;
                return 's:'.strlen($propname) . ':"' . $propname . '"';
            }, $ser_data);
          
            // replace object-names
            $ser_data = preg_replace('/O:\d+:"[^"]++"/', 'O:' . strlen($class) . ':"' . $class . '"', $ser_data);
            return unserialize($ser_data);
        }
        /**
         * Retourne l'URL
         * @return string
         */
        public static  function getUrl()
        {
            $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $CurPageURL = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['SERVER_NAME'];
            //  EasyFrameWork::Debug($_SERVER["SCRIPT_NAME"]);
            $url = explode("/", $_SERVER["REQUEST_URI"]);
            return $protocol . $_SERVER['HTTP_HOST'] . str_replace("/" . end($url), "", $_SERVER["REQUEST_URI"]);
        }
        /**
         * Compare deux date (retourne 1 si d1>d2 sinon 0)
         * @param string $d1
         * @param string $d2
         * @return int
         */
        public static function DateCompare($d1,$d2){
            $date1 = new DateTime($d1);
            $date2 = new DateTime($d2); // Can use date/string just like strtotime.
            return $date1 > $date2?1:0;
        }
        /**
         * Permet de générer un mot de passe de la longeur indiquée
         * @param int $len
         * @return string
         */
        public static function generatePassWord($len){
            $alpha="ABDCEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+=-";
            $mdp="";
            for($i=0;$i<$len;$i++){
                $mdp.=$alpha[mt_rand(0,strlen($alpha)-1)];
            }
            return $mdp;
        }
        /**
         * Modifie le template pour ajouter une alert _alert et redirige vers la page indiquée
         * @param EasyTemplate $template
         * @param string $message
         * @param string $url
         * @return void
         */
        public static function redirectWithAlert($template, $message,$url)
        {
            $template->addStylesheet("_css/alert.css");
            $template->addScript("_js/alert.js");
            $template->getRessourceManager()->addDirectJs("$(function(){
                    _alert('$message',function(){
                        window.location.href=\"$url\";
                    });
                });");
        }
    }
