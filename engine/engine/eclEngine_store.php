<?php

class EclEngine_store
{
    private $drivers = [];

    public function __get($name)
    {
        if (array_key_exists($name, $this->drivers)) {
            return $this->drivers[$name];
        }else{
            $class = "eclStore_" . $name;
            $this->drivers[$name] = new $class();
            return $this->drivers[$name];
        }
    }

}

?>