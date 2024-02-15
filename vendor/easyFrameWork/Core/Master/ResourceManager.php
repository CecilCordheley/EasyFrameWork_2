<?php
namespace vendor\easyFrameWork\Core\Master;
class ResourceManager {
    private $scripts = [];
    private $stylesheets = [];

    public function addScript(string $scriptPath) {
        $this->scripts[] = $scriptPath;
    }

    public function addStylesheet(string $stylesheetPath) {
        $this->stylesheets[] = $stylesheetPath;
    }

    public function renderScripts(string &$content) {
        foreach ($this->scripts as $script) {
            $content=str_replace("</head>","<script src=\"$script\"></script>\n</head>",$content);
        }
        
    }

    public function renderStylesheets(string &$content) {
        foreach ($this->stylesheets as $style) {
            $content=str_replace("</title>","</title>\n<link rel=\"stylesheet\" href=\"$style\">",$content);
        }
    }
}