<?php

class EclEngine_store
{
    private $drivers = [];
    public eclStore_control $control;
    public eclStore_moduleTemplate $moduleTemplate;

    public function __construct()
    {
        $this->control = new eclStore_control();
        $this->moduleTemplate = new eclStore_moduleTemplate();
    }

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