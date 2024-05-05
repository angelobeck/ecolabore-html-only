<?php

class eclStore_moduleTemplate
{
    protected $cache = [];

    public function open(string $name): string
    {
        if (!isset($this->cache[$name])) {
            [$prefix, $module] = explode('_', $name);
            $path = PATH_LIBRARY . $module . '/' . $name . '.html';
            if (is_file($path)) {
                $this->cache[$name] = file_get_contents($path);
            } else {
                $this->cache[$name] = '';
            }
        }
        return $this->cache[$name];
    }

}
