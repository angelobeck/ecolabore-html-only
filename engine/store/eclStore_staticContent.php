<?php

class EclStore_staticContent
{
    protected $cache = [];

    public function open(string $name): array
    {
        if (!isset($this->cache[$name])) {
            [$module, $name] = explode('_', $name);
            $path = PATH_LIBRARY . $module . '/_staticContents/' . $name . '.json';
            if (is_file($path)) {
                $contents = file_get_contents($path);
                $this->cache[$name] = eclIo_convert::json2array($contents, $path);
            } else {
                $this->cache[$name] = [];
            }
        }
        return $this->cache[$name];
    }

}
