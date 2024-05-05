<?php

class eclEngine_renderModuleContainer
{
    public eclMod $module;
    public array $slot;
    public array $vars = [];

    public function __construct(eclMod $module, array $slot = [])
    {
        $this->module = $module;
        $this->slot = $slot;
    }

}
