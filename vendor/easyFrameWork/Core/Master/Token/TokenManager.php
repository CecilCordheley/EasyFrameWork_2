<?php

namespace vendor\easyFrameWork\Core\Master\Token;

use Exception;
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;

class TokenUSer
{
    private $attr = ["UUID" => "", "secret" => "", "login" => "", "data" => []];
    public function __get($name): mixed
    {
        if (array_key_exists($name, $this->attr)) {
            return $this->attr[$name];
        } else {
            throw new Exception("$name is not a valid Key");
        }
    }
    public function __invoke()
    {
        return $this->attr;
    }
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->attr)) {
            $this->attr[$name] = $value;
        } else {
            throw new Exception("$name is not a valid Key");
        }
    }
    public function __toString()
    {
        return json_encode($this->attr);
    }
}
class TokenManager
{
    private $current;
    private $filename;
    public function __construct($filename)
    {
        if (file_exists($filename)) {
            $this->filename = $filename;
            $data = file_get_contents($filename);
            if ($data != "")
                $this->current = json_decode($data, true);
            else
                $this->current = [];
            //   EasyFrameWork::Debug($this->current);
        } else {
            throw new Exception("$filename doesn't exsit in the current context");
        }
    }
    public function commit():bool
    {
        $data = json_encode($this->current);
        return file_put_contents($this->filename, $data);
    }
    public function createUser($name, $data = []):mixed
    {
        if (!$this->checkUser($name)) {
            $t = new TokenUSer;
            $t->UUID = uniqid();
            $t->login = $name;
            $t->data = $data;
            $t->secret = self::secret();
            $this->current[] = $t();
            if ($this->commit()) {
                return ["result" => "OK", "secret" => $t->secret];
            } else {
                return false;
            }
        } else {
            throw new Exception("$name already exist");
        }
    }
    public function getUser($name):mixed{
        $checkUser = function ($user) use ($name) {
            //   echo "{$user["secret"]} - $secret";
            return ($user["login"] == $name);
        };
        $filteredUsers = array_filter($this->current, $checkUser);
        if(count($filteredUsers)>0){
            return $filteredUsers;
        }else{
            return false;
        }
    }
    public function update($name, $secret, $t)
    {
        $user = $this->Connect($name, $secret);
        if ($user != false) {
            $this->current[$user["index"]] = $t();
            $this->commit();
        } else {
            throw new Exception("Token User not found");
        }
    }
    public function getToken($tk): mixed
    {
        $c = new Cryptographer;
        $el = json_decode($c->decrypt($tk, session_id()), true);
        // EasyFrameWork::Debug($el);
        if ($el != null) {
            $UUID = $el["UUID"];
            //  echo "{$el["TimeEnd"]} -  {$el["timeStart"]}";
            if (($el["TimeEnd"] - time()) > 0) {
                // Création de la fonction de vérification de l'utilisateur
                $checkUser = function ($user) use ($UUID) {
                    return ($user["UUID"] == $UUID);
                };

                // Filtrage du tableau $this->current pour trouver l'utilisateur correspondant
                $filteredUsers = array_filter($this->current, $checkUser);

                if (count($filteredUsers) == 1) {
                    return $filteredUsers[0];
                } else {
                    return 0;
                }
            } else
                return -1;
        } else
            return -1;
    }
    public function Connect($name, $secret)
    {
        // Création de la fonction de vérification de l'utilisateur
        $checkUser = function ($user) use ($name, $secret) {
            //   echo "{$user["secret"]} - $secret";
            return ($user["login"] == $name && $user["secret"] == $secret);
        };

        // Filtrage du tableau $this->current pour trouver l'utilisateur correspondant
        $filteredUsers = array_filter($this->current, $checkUser);
        //  EasyFrameWork::Debug($filteredUsers);
        // Vérification si un utilisateur correspondant a été trouvé
        if (!empty($filteredUsers)) {
            // Récupération du premier utilisateur correspondant
            $user = reset($filteredUsers);

            // Encodage JSON des données de l'utilisateur
            $return = json_encode(["UUID" => $user["UUID"], "timeStart" => time(), "TimeEnd" => time() + 3600]);

            // Cryptage des données encodées
            $c = new Cryptographer;
            $tk = $c->encrypt($return, session_id());

            // Retour des résultats
            return ["result" => "OK", "tk" => $tk];
        } else {
            // Aucun utilisateur correspondant trouvé
            return false;
        }

    }
    public function checkUser($name): bool
    {
        $a = array_filter($this->current, function ($el) use ($name) {
            return $el["login"] == $name;
        });
        return (count($a) == 1);
    }
    private static function secret(): string
    {
        $r = "";
        $c = new Cryptographer;
        $r = $c->hashString(time(), session_id());
        $r = str_replace("/", "0", $r);
        $r = str_replace("+", "0", $r);
        $r = str_replace("-", "0", $r);
        $r = str_replace("\\", "0", $r);
        return substr($r, 0, 12);
    }
}
