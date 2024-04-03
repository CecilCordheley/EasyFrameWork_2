<?php
namespace vendor\easyFrameWork\Core\Master;

class Autoloader
{
    /**
     * Enregistre notre autoloader
     */
    public static function register()
    {
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    /**
     * Charge le fichier correspondant à notre classe si celui-ci existe
     * @param string $class Le nom complet de la classe à charger
     */
    public static function autoload(string $class)
    {
        // Convertir les barres obliques en barres obliques inverses pour être compatibles avec le système de fichiers
        $class = str_replace('\\', "/", $class);
        $file =   $class . '.php';
        //echo $file;
        if (file_exists($file)) {
            require_once $file;
        }
    }
}
