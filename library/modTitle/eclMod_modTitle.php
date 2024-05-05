<?php

class eclMod_modTitle extends eclMod
{
    public $title;

    public function connectedCallback(): void
    {
        $this->title =$this->document->application->name;
    }
}
