<?php

class eclMod
{
    public eclEngine_document $document;

    public function __construct(eclEngine_document $document)
    {
        $this->document = $document;
    }

    public function connectedCallback(): void
    {

    }

}
