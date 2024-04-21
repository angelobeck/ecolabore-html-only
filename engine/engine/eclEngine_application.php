<?php

class eclEngine_application
{

    public $name;
    public $parent;
    public $helper;
    public $data = [];
    public $map = [];
    public $path = [];
    public $access = 0;
    public $groups = [];
    public $ignoreSubfolders = false;
    public $domainName = '';
    public $userName = '';

    protected $childrenAll = [];
    protected $childrenIsLoaded = false;
    protected $childrenByName = [];

    public function __construct(eclEngine_application|bool $parent = false, string $name = "-root", string $helperName = "system")
    {
        global $store;
        $this->parent = $parent;
        $this->name = $name;
        $this->helper = 'eclApp_' . $helperName;
        if ($parent) {
            $this->path = [...$parent->path, $this->name];
            $this->access = $parent->access;
            $this->groups = $parent->groups;
            $this->domainName = $parent->domainName;
            $this->userName = $parent->userName;
        }
        if (isset($this->helper::$control)) {
            $this->data = $store->control->open($this->helper::$control);
        }
        if (isset($this->helper::$map)) {
            $this->map = $this->helper::$map;
        }
        if (is_callable([$this->helper, 'constructorHelper'])) {
            $this->helper::constructorHelper($this);
        }
    }

    public function child(string $name): eclEngine_application|null
    {
        if (isset($this->childrenByName[$name])) {
            return $this->childrenByName[$name];
        } elseif ($this->childrenIsLoaded) {
            return null;
        }
        foreach ($this->map as $helperName) {
            $helper = "eclApp_" . $helperName;
            if (
                (isset($helper::$name) && $helper::$name === $name)
                || (is_callable([$helper, 'isChild']) && $helper::isChild($this, $name))
            ) {
                $this->childrenByName[$name] = new eclEngine_application($this, $name, $helperName);
                return $this->childrenByName[$name];
            }
        }
        return null;
    }

    public function children(): array
    {
        if (!$this->childrenIsLoaded) {
            $this->childrenIsLoaded = true;
            foreach ($this->map as $helperName) {
                $helper = "eclApp_" . $helperName;
                if (is_callable([$helper, 'childrenNames'])) {
                    $childrenNames = $helper::childrenNames($this);
                } elseif (isset($helper::$name)) {
                    $childrenNames = [$helper::$name];
                } else {
                    continue;
                }
                foreach ($childrenNames as $name) {
                    if (!isset($this->childrenByName[$name])) {
                        $this->childrenByName[$name] = new eclEngine_application($this, $name, $helperName);
                    }
                    $this->childrenAll[] = $this->childrenByName[$name];
                }
            }
        }
        return $this->childrenAll;
    }

    public function refresh(): void
    {
        $this->childrenAll = [];
        $this->childrenByName = [];
        $this->childrenIsLoaded = false;
    }

    public function dispatch(eclEngine_document $document): void
    {
        if (is_callable([$this->helper, 'dispatch'])) {
            $this->helper::dispatch($document);
        }
    }

}
