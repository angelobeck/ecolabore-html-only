<?php

class eclEngine_render
{
    private eclEngine_document $document;
    private eclEngine_renderTokenizer $tokenizer;
    private eclEngine_renderParser $parser;

    public function __construct(eclEngine_document $document)
    {
        $this->document = $document;
        $this->tokenizer = new eclEngine_renderTokenizer();
        $this->parser = new eclEngine_renderParser();
    }

    public function render(eclMod $module, array $params = [], array $slot = []): string
    {
        global $store;
        $templateName = get_class($module);
        $template = $store->moduleTemplate->open($templateName);
        $tokens = $this->tokenizer->tokenize($template);
        $children = $this->parser->parse($tokens, $module, $templateName, $slot);
        foreach ($params as $key => $value) {
            $module->$key = $value;
        }
        $module->connectedCallback();
        return $this->renderChildren($children);
    }

    private function renderChildren(array $children): string
    {
        $buffer = '';
        foreach ($children as $node) {
            switch ($node->type) {
                case 'static_content':
                    $buffer .= $node->value;
                    break;

                case 'dinamic_content':
                    $buffer .= $this->getProperty($node, $node->value, true);
                    break;

                default:
                    if (isset($node->dinamicAttributes['if:true']) and !$this->getProperty($node, $node->dinamicAttributes['if:true']))
                        break;
                    if (isset($node->dinamicAttributes['if:false']) and $this->getProperty($node, $node->dinamicAttributes['if:false']))
                        break;

                    switch ($node->value) {
                        case 'mod':
                            if (isset($node->staticAttributes['name']))
                                $name = $node->staticAttributes['name'];
                            else if (isset($node->dinamicAttributes['name']))
                                $name = $this->getProperty($node, $node->dinamicAttributes['name']);
                            else
                                break;

                            $module = $this->document->mod->$name;
                            $params = [];
                            foreach ($node->staticAttributes as $attribute => $value) {
                                if (strpos($attribute, ':') === false and $attribute !== 'name')
                                    $params[$attribute] = $value;
                            }
                            foreach ($node->dinamicAttributes as $attribute => $value) {
                                if (strpos($attribute, ':') === false and $attribute !== 'name')
                                    $params[$attribute] = $this->getProperty($node, $value);
                            }

                            $buffer .= $this->render($module, $params, $node->children);
                            break;

                        case 'slot':
                        case 'template':
                        default:
                            $buffer .= '<' . $node->value . '';
                            foreach ($node->staticAttributes as $attribute => $value) {
                                if (strpos($attribute, ':') === false)
                                    $buffer .= ' ' . $attribute . '="' . $value . '"';
                            }
                            foreach ($node->dinamicAttributes as $attribute => $value) {
                                if (strpos($attribute, ':') === false)
                                    $buffer .= ' ' . $attribute . '="' . $this->getProperty($node, $value, true) . '"';
                            }

                            $buffer .= '>';
                            if (!$node->closingTag)
                                break;

                            $buffer .= $this->renderChildren($node->children);
                            $buffer .= '</' . $node->value . '>';
                    }
            }
        }
        return $buffer;
    }

    private function getProperty(eclEngine_renderNode $node, string $path, bool $returnString = false)
    {
        $current = $node->module->module;
        $parts = explode('.', $path);
        foreach ($parts as $name) {
            if (!preg_match('/[a-zA-z][a-zA-z0-9_]*/', $name))
                return '';
            if (isset($current->$name)) {
                $current = $current->$name;
            } elseif (is_callable([$current, $name])) {
                $current = $current->$name();
            } else {
                return '';
            }
        }
        if ($returnString) {
            if (is_string($current))
                return $current;
            elseif (is_numeric($current))
                return strval($current);
            else if ($current === true)
                return 'true';
            else if ($current === false)
                return 'false';
            else
                return '';
        }
        return $current;
    }

}
