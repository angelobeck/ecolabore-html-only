<?php

class eclMod_modHtml extends eclMod
{
        public string $lang;
        public string $title;

        public function connectedCallback(): void
        {
                $this->lang = 'pt';
                $this->title = 'olá';
        }

}
