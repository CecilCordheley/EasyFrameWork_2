<?php

namespace vendor\easyFrameWork\Core\Master;

use Exception;
use ReturnTypeWillChange;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;

class Token
{
    private string $oauth = "";
    private string $userName = "";
    private array $data;
    private string $update;
    public function __construct($userName, $token, $data = [])
    {
        $this->oauth = $token;
        $this->userName = $userName;
        $this->data = $data;
        $this->update = time();
    }
    public function getArray()
    {
        return ["userName" => $this->userName, "Oauth" => $this->oauth, "lastUpdate" => $this->update, "data" => $this->data];
    }
    public function addData($key, $value)
    {
        $this->data[$key] = $value;
    }
    public function __toString()
    {
        $a = ["userName" => $this->userName, "Oauth" => $this->oauth, "data" => $this->data];
        return json_encode($a);
    }
}
class AccessToken
{
    private $file;
    public function __construct($filename)
    {
        if (isset($filename)) {
            $this->file = $filename;
        } else {
            throw new Exception("$filename doesn't exist in the current context");
        }
    }
    public function handle($user, $secret)
    {
        $crypt = new Cryptographer;
        $json = json_decode(file_get_contents($this->file));
        $i = -1;
        $b = false;
        while (!$b && $i < count($json)) {
            $i++;
            $b = ($json[$i]["username"] == $user && $json[$i]["secret"] == $crypt->hashString($secret, $user));
        }
        return $b;
    }
    public function getSecret()
    {
        $crypt = new Cryptographer;
        $id = uniqid();
        return substr($crypt->hashString($id, session_id(), Cryptographer::HASH_ALGO["MD2"]), 0, 12);
    }
    public function setPassWord($user){
        $crypt = new Cryptographer;
        $secret = $this->getSecret();
        $mdp = $crypt->hashString($secret, $user);
        $json = json_decode(file_get_contents($this->file));
        $i = -1;
        $b = false;
        if (count($json))
            while (!$b && $i < count($json)-1) {
                $i++;
                $b = ($json[$i]["username"] == $user);
            }
        if (!$b) {
            $json[] = [
                "username" => $user,
                "secret" => $mdp
            ];
            file_put_contents($this->file, json_encode($json));
            return $secret;
        } else {
            throw new Exception("$user already set");
        }
    }
    public function createAccessToken($user)
    {
        $crypt = new Cryptographer;
        $secret = $this->getSecret();
        $mdp = $crypt->hashString($secret, $user);
        $json = json_decode(file_get_contents($this->file));
        $i = -1;
        $b = false;
        if (count($json))
            while (!$b && $i < count($json)-1) {
                $i++;
                $b = ($json[$i]["username"] == $user);
            }
        if (!$b) {
            $json[] = [
                "username" => $user,
                "secret" => $mdp
            ];
            file_put_contents($this->file, json_encode($json));
            return $secret;
        } else {
            throw new Exception("$user already set");
        }
    }
}
class tokenManager
{
    private static $file = "tokens.json";
    public static function setFile(string $file)
    {
        if (file_exists($file)) {
            self::$file = $file;
            return true;
        } else
            return false;
    }
    public function createProfile($user)
    {
        $access = new AccessToken("access.json");
        
       $secret= $access->createAccessToken($user);
        return "{\"result\":\"OK\",\"secret\":\"$secret\"}";
    }
    private Cryptographer $crypt;
    private array $tokens;
    public function __construct()
    {
        $this->crypt = new Cryptographer;
        $this->tokens = json_decode(file_get_contents(self::$file), true);
    }
    public function generateToken():string
    {
        $token = $this->crypt->hashString(time(), session_id(), Cryptographer::HASH_ALGO["MD2"]);
        return $token;
    }
    public function createToken($userName, $toJson = true)
    {
        $result = [];
        $tk = $this->generateToken();
        $user = new Token($userName, $this->generateToken(), []);
        $this->tokens[] = $user->getArray();
        if ($this->commit()) {
            $result["result"] = "OK";
            $result["token"] = $tk;
        } else {
            $result["result"] = "KO";
            $result["error"] = "Token has not been created";
        }
        if ($toJson)
            return json_encode($result);
        else
            return $result;
    }
    public function getSession($token, $toJson = true)
    {
        $result = [];
        $i = -1;
        $b = false;
        while (!$b && $i < count($this->tokens) - 1) {
            $i++;
            //  echo $this->tokens[$i]["Oauth"]." - $token";
            $b = $this->tokens[$i]["Oauth"] == $token;
        }
        if ($b) {
            $tk = $this->tokens[$i];
            $Token = new Token($tk["userName"], $tk["Oauth"], $tk["data"]);
            $result["result"] = "OK";
            $result["session"] = $Token;
        } else {
            $result["result"] = "KO";
            $result["error"] = "not a valid Token";
        }
        if ($toJson)
            return json_encode($result);
        else
            return $result;
    }
    public function getToken($userName, $toJson = true)
    {
       // echo "PO";
        $result = [];
        $i = -1;
        $b = false;
        while (!$b && $i < count($this->tokens)) {
            $i++;
            $b = $this->tokens[$i]["userName"] == $userName;
        }
        if ($b) {
            $tk = $this->tokens[$i]["Oauth"];
            $result["result"] = "OK";
            $result["token"] = $tk;
        } else {
            $result["result"] = "KO";
            $result["error"] = "not a valid userName";
        }
        if ($toJson)
            return json_encode($result);
        else
            return $result;
    }
    private function findBy($key, $value)
    {
        $i = -1;
        $b = false;
        while (!$b && $i < count($this->tokens) - 1) {
            $i++;
            //  echo $this->tokens[$i]["Oauth"]." - $token";
            $b = $this->tokens[$i][$key] == $value;
        }
        if ($b) {
            return ["index" => $i, "token" => $this->tokens[$i]];
        } else
            return false;
    }
    public function update(Token $token)
    {
        $t = $token->getArray();
        $i = $this->findBy("Oauth", $t["Oauth"])["index"];
        $this->tokens[$i] = $t;
        $this->commit();
    }
    private function commit()
    {
        $str = json_encode($this->tokens);
        return file_put_contents(self::$file, $str);
    }
}
