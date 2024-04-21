<?php

class EclStore_control
{
    protected $cache = [];

    public function open(string $name): array
    {
        if (!isset($this->cache[$name])) {
            [$module, $control] = explode('_', $name);
            $path = PATH_LIBRARY . $module . '/_controls/' . $control . '.php';
            if (is_file($path)) {
                $this->cache[$name] = include $path;
            } else {
                $this->cache[$name] = [];
            }
        }
        return $this->cache[$name];
    }

}
