<?php

namespace vendor\easyFrameWork\Core\Master;

use vendor\easyFrameWork\Core\Master\ResourceManager;

class EasyTemplate
{
    private $config;
    private $content;
    private $variables = [];
    private $loops = [];
    private $resourceManager;

    public function __construct(array $config, ResourceManager $resourceManager)
    {
        echo "<!--Initilize Template-->\n";
        $this->config = $config;
        $this->resourceManager = $resourceManager;
        $this->loadContent();
    }

    private function loadContent()
    {
        if (file_exists($this->config['templateDirectory'] . '/' . $this->config['masterPage'])) {
            $this->content = file_get_contents($this->config['templateDirectory'] . '/' . $this->config['masterPage']);
            echo "<!--Master Template Loaded-->\n";
        } else
            echo "<!--Error Master Template Can't be Load-->\n";
    }
    public function changeMaster($newMaster)
    {
        $this->content = file_get_contents($this->config['templateDirectory'] . '/' . $newMaster);
    }
    public function setVariables(array $variables)
    {
        $this->variables = $variables;
    }
    public function remplaceTemplate($key, $tpl)
    {
        $c = file_get_contents($this->config['templateDirectory'] . "/$tpl");
        $this->content = str_replace("{var:$key}", $c, $this->content);
    }
    public function render(array $customReplacements = [])
    {
        echo "<!--Render Template-->\n";
        $this->renderStylesheets();
        $this->renderScripts();
        $this->renderMicroData();
        $this->replaceVariables();
        $this->replaceCondition();
        foreach ($this->loops as $key => $loop) {
            $this->replaceLoop($key, $loop);
        }


        $this->replaceRootURL();
        $this->replaceImageURL();
        $this->replaceSessionVariables();

        // Exécute les méthodes de substitution personnalisées
        // var_dump($customReplacements);
        foreach ($customReplacements as $customReplacement) {
            //var_dump(gettype($customReplacement));
            if (is_callable([$customReplacement])) {

                call_user_func_array($customReplacement, [&$this]);
            }
        }
        $this->clear();
        //  var_dump($this->content);
        echo $this->content;
    }
    private function clear()
    {
        $this->content = preg_replace("/\{var:(.*?)\}/i", "", $this->content);
    }
    public function addScript($scriptPath)
    {
        $this->resourceManager->addScript($scriptPath);
    }
    public function renderMicroData(){
        $this->resourceManager->renderMicroData($this->content);
    }
    public function addStylesheet($stylesheetPath)
    {
        $this->resourceManager->addStylesheet($stylesheetPath);
    }

    public function renderScripts()
    {
        $this->resourceManager->renderScripts($this->content);
    }

    public function renderStylesheets()
    {
        $this->resourceManager->renderStylesheets($this->content);
    }
    public function setLoop(string $key, array $a)
    {
        $this->loops[$key] = $a;
    }
    public function getRessourceManager():ResourceManager{
        return $this->resourceManager;
    }
    private function replaceCondition()
    {
        $pattern = "/\{\:IF (.*?)(=|!|>|<)(.*?)\}(.*?)(?:\{\:ELSE\:\}(.*?))?\{\:\/IF\}/s";
        if (preg_match_all($pattern, $this->content, $matches)) {
            // var_dump($matches);
            for ($i = 0; $i < count($matches[0]); $i++) {
                $replace = $matches[5][$i] ?? "";

                switch ($matches[2][$i]) {
                    case "=":
                        $replace = ($matches[1][$i] == $matches[3][$i]) ? $matches[4][$i] : $replace;
                        break;
                    case ">":
                        $replace = ($matches[1][$i] > $matches[3][$i]) ? $matches[4][$i] : $replace;
                        break;
                    case "<":
                        $replace = ($matches[1][$i] < $matches[3][$i]) ? $matches[4][$i] : $replace;
                        break;
                    case "!": {
                            $replace = ($matches[1][$i] !== $matches[3][$i]) ? $matches[4][$i] : $replace;
                            break;
                        }
                }
                $this->content = str_replace($matches[0][$i], $replace, $this->content);
            }
        }
    }
    private function replaceLoop(string $key, array $array, bool $UTF8Encode = false)
    {
        $pattern = "\\{LOOP:$key\\}(.*?)\\{\\/LOOP\\}";
        if (preg_match_all("/$pattern/is", $this->content, $matches)) {
            $content = array_reduce($array, function ($html, $lines) use ($matches, $UTF8Encode) {
                $html .= $matches[1][0];
                foreach ($lines as $key => $value) {
                    if (gettype($value) != "array")
                        if ($UTF8Encode)
                            $html = str_replace("{#$key#}", mb_convert_encoding($value, "UTF-8"), $html);
                        else
                            $html = str_replace("{#$key#}", $value, $html);
                }
                return $html;
            }, "");
            $this->content = str_replace($matches[0][0], $content, $this->content);
        }
    }
    private function replaceVariables()
    {
        // var_dump($this->variables);
        foreach ($this->variables as $key => $value) {

            $arr = gettype($value);
            if ($arr == "string")
                $this->content = str_replace("{var:$key}", html_entity_decode($value), $this->content);
            else {
                foreach ($value as $sKey => $sValue) {
                    if (gettype($sValue) == "string")
                        $this->content = str_replace("{var:$key.$sKey}", html_entity_decode(htmlentities($sValue)), $this->content);
                }
            }
        }
    }

    private function replaceRootURL()
    {
        $this->content = str_replace("{:racine}", $this->config['racineProject'] . '/', $this->content);
    }

    private function replaceImageURL()
    {
        $this->content = str_replace("{:image}", $this->config['imageDirectory'], $this->content);
    }

    private function replaceSessionVariables()
    {
        if (isset($_SESSION)) {
            foreach ($_SESSION as $context => $values) {
                if (gettype($values) == "array")
                    foreach ($values as $name => $value) {
                        $this->content = str_replace("{:SESSION context=\"$context\" name=\"$name\"}", htmlspecialchars($value), $this->content);
                        $this->content = str_replace("{:SESSION name=\"$name\" context=\"$context\"}", htmlspecialchars($value), $this->content);
                    }
            }
        }
    }
}