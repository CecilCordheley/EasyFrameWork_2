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
        $this->config = $config;
        $this->resourceManager = $resourceManager;
        $this->loadContent();
    }

    private function loadContent()
    {
        $this->content = file_get_contents($this->config['templateDirectory'] . '/' . $this->config['masterPage']);
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
        $this->renderStylesheets();
        $this->renderScripts();
        foreach ($this->loops as $key => $loop) {
            $this->replaceLoop($key, $loop);
        }
        $this->replaceVariables();
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

        echo $this->content;
    }
    public function addScript($scriptPath)
    {
        $this->resourceManager->addScript($scriptPath);
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
        foreach ($this->variables as $key => $value) {
            $this->content = str_replace("{var:$key}", htmlspecialchars($value), $this->content);
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
                foreach ($values as $name => $value) {
                    $this->content = str_replace("{:SESSION context=\"$context\" name=\"$name\"}", htmlspecialchars($value), $this->content);
                }
            }
        }
    }
}