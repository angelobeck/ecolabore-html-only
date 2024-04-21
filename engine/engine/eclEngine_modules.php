<?php

class EclEngine_modules
{
    private $modules = [];
    private $document;

    public function __construct(eclEngine_document $document)
    {
        $this->document = $document;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->modules)) {
            return $this->modules[$name];
        } else {
            $class = "eclMod_mod" . ucfirst($name);
            $this->modules[$name] = new $class($this->document);
            return $this->modules[$name];
        }
    }

    public function __set($name, $module)
    {
        $this->modules[$name] = $module;
    }

}
