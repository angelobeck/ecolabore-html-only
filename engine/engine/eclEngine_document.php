<?php

class eclEngine_document
{
    public eclEngine_application $application;
    public string $lang;
    public $modules;

    public function __construct()
    {
        $this->modules = new eclEngine_modules($this);
    }

    public function route(array $path): void
    {
        global $system;
        if(count($path) === 1) {
            $path[] = '-index';
        }
        $this->application = $this->routeSubfolders($system, $path);
    }

    private function routeSubfolders(eclEngine_application $application, array $path): eclEngine_application|null
    {
        if (count($path) === 0) {
            return $application;
        }
        if ($application->ignoreSubfolders) {
            return $application;
        }
        $name = array_shift($path);
        $child = $application->child($name);
        if ($child) {
            $child = $this->routeSubfolders($child, $path);
            if ($child) {
                return $child;
            }
        }
        $child = $application->child('-default');
        if ($child) {
            return $this->routeSubfolders($child, $path);
        }
        return null;
    }

    public function sessionStart(): void
    {

    }

    public function dispatch(): void
    {
        $this->application->dispatch($this);
    }

    public function render(): void
    {

    }

    public function selectLanguage(array|string $text): string
    {
        if (is_string($text)) {
            return $text;
        }
        if (!is_array($text)) {
            return "";
        }
        if ($text[$this->lang]) {
            return $text[$this->lang];
        }
        return "";
    }

}
