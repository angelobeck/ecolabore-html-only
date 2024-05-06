<?php

class eclEngine_document
{
    public eclEngine_application $application;
    public string $lang;
    public eclEngine_modules $mod;
    public eclEngine_render $render;
    public string $buffer = '';

    public string $protocol;
    public string $host;
    public array $path;
    public array $actions = [];

    public function __construct()
    {
        $this->mod = new eclEngine_modules($this);
        $this->render = new eclEngine_render($this);
    }

    public function route(array $path): void
    {
        global $system;
        if (count($path) === 1) {
            $path[] = '-home';
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
        $this->buffer = $this->render->render($this->mod->html);
    }

    public function selectLanguage(array|string $text): array
    {
        if (is_string($text)) {
            return ['content' => $text, 'lang' => $this->lang];
        }
        if (is_number($text)) {
            return ['content' => strval($text), 'lang' => $this->lang];
        }
        if (!is_array($text)) {
            return ['content' => "", 'lang' => $this->lang];
        }
        if (isset($text[$this->lang])) {
            return [...$text[$this->lang], 'lang' => $this->lang];
        }
        foreach ($text as $lang => $value) {
            return [...$value, 'lang' => $lang];
        }
        return ['content' => "", 'lang' => $this->lang];
    }

    public function url(array|bool $path = true, string|bool $lang = true, string|bool $actions = false): string
    {
        if (!is_array($path)) {
            $path = $this->application->path;
        }
        if (count($path) === 0) {
            array_unshift($path, SYSTEM_DEFAULT_DOMAIN_NAME);
        }
        if ($lang === true) {
            $lang = $this->lang;
        }
        if ($lang !== SYSTEM_DEFAULT_LANGUAGE) {
            $path[] = '-' . $lang;
        }
        if (is_string($actions)) {
            $path[] = $actions;
        }

        if (SYSTEM_HOSTING_MODE === 'single') {
            if ($path[0] === SYSTEM_DEFAULT_DOMAIN_NAME)
                array_shift($path);
            $host = $this->protocol . '//' . $this->host . '/';
        } elseif (SYSTEM_HOSTING_MODE === 'subdomains') {
            $domain = array_shift($path);
            if ($domain === SYSTEM_DEFAULT_DOMAIN_NAME)
                $host = $this->protocol . '//' . $this->host . '/';
            else
                $host = $this->protocol . '//' . $domain . '.' . $this->host . '/';
        } else {
            $host = $this->protocol . '//' . $this->host . '/';
        }
        $url = implode('/', $path);
        if (!SYSTEM_REWRITE_ENGINE) {
            if (strlen($url))
                $url = '?url=' . $url;
            $url = SYSTEM_SCRIPT_NAME . $url;
        }

        return $host . $url;
    }

}
