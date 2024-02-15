<?php

namespace vendor\easyFrameWork\Core\Master;

use vendor\easyFrameWork\Core\Master\EasyTemplate;
use vendor\easyFrameWork\Core\Master\Router;
use vendor\easyFrameWork\Core\Master\Controller;
use vendor\easyFrameWork\Core\Master\Autoloader;

abstract class EasyFrameWork
{
    public static $Racines = [];
    public static function INIT()
    {
        // echo "INIT FRAMEWORK---";
        require_once("Autoloader.php");
        self::$Racines = json_decode(file_get_contents("vendor/easyFrameWork/Core/config/config.json"), true)["racine"];
        //   EasyFrameWork::Debug(self::$Racines);
    }
    public static function Debug(mixed $var)
    {
        var_dump($var);
        exit;
    }
    private static $classes = [];

    // Méthode pour enregistrer une classe dans le tableau
    public static function registerClass(string $className, $classInstance) {
        self::$classes[$className] = $classInstance;
    }

    // Méthode pour obtenir une instance de classe à partir du nom de classe
    public static function getClassInstance($className) {
        if (isset(self::$classes[$className])) {
            return self::$classes[$className];
        } else {
            throw new \Exception("Classe '$className' non enregistrée.");
        }
    }
}
class FileParser
{
}
class Debug
{
}
class Cryptographer
{
    public const HASH_ALGO = [
        "MD2" => "md2",
        "MD4" => "md4",
        "MD5" => "md5",
        "SHA1" => "sha1",
        "SHA256" => "sha256",
        "SHA384" => "sha384",
        "SHA512" => "sha512",
        "RIPEMD128" => "ripemd128",
        "RIPEMD160" => "ripemd160",
        "RIPEMD256" => "ripemd256",
        "RIPEMD320" => "ripemd320",
        "WHIRLPOOL" => "whirlpool",
        "TIGER128,3" => "tiger128,3",
        "TIGER160,3" => "tiger160,3",
        "TIGER192,3" => "tiger192,3",
        "TIGER128,4" => "tiger128,4",
        "TIGER160,4" => "tiger160,4",
        "TIGER192,4" => "tiger192,4",
        "SNEFRU" => "snefru",
        "GOST" => "gost",
        "ADLER32" => "adler32",
        "CRC32" => "crc32",
        "CRC32B" => "crc32b",
        "HAVAL128,3" => "haval128,3",
        "HAVAL160,3" => "haval160,3",
        "HAVAL192,3" => "haval192,3",
        "HAVAL224,3" => "haval224,3",
        "HAVAL256,3" => "haval256,3",
        "HAVAL128,4" => "haval128,4",
        "HAVAL160,4" => "haval160,4",
        "HAVAL192,4" => "haval192,4",
        "HAVAL224,4" => "haval224,4",
        "HAVAL256,4" => "haval256,4",
        "HAVAL128,5" => "haval128,5",
        "HAVAL160,5" => "haval160,5",
        "HAVAL192,5" => "haval192,5",
        "HAVAL224,5" => "haval224,5",
        "HAVAL256,5" => "haval256,5"
    ];
    public function encrypt($string, $key)
    {
        $ciphering = "AES-128-CTR";
        $iv_length = openssl_cipher_iv_length($ciphering);
        $encryption_iv = '1234567891011121';
        $encryption = openssl_encrypt(
            $string,
            $ciphering,
            $key,
            0,
            $encryption_iv
        );
        return $encryption;
    }
    public function hashString(string $str, string $key = "", string $algo = "sha256"): string
    {
        $return = hash($algo, $str);
        if ($key != "") {
            return self::encrypt($return, $key);
        } else
            return $return;
    }
    public static function decrypt(string $content, string $key): string
    {
        $ciphering = "AES-128-CTR";
        $iv_length = openssl_cipher_iv_length($ciphering);
        $encryption_iv = '1234567891011121';
        $encryption = openssl_decrypt(
            $content,
            $ciphering,
            $key,
            0,
            $encryption_iv
        );
        return $encryption;
    }
}
