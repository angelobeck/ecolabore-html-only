<?php

class eclEngine_renderNode
{
    public eclEngine_renderModuleContainer $module;
    public eclEngine_renderNode | null $parent;
    public string $type;
    public string $value;
    public array $staticAttributes = [];
    public array $dinamicAttributes = [];
    public array $children = [];
    public bool $closingTag = true;

    public function __construct(eclEngine_renderNode | null $parent, string $type, string $value)
    {
        $this->parent = $parent;
        $this->type = $type;
        $this->value = $value;
        if($parent !== null) {
            $this->module = $parent->module;
        }
    }
    
}
