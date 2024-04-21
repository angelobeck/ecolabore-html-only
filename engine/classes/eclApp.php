<?php

class EclApp
{
    static $map = [];

    static function isChild(eclEngine_application $parent, string $name): bool
    {
        return false;
    }

    static function childrenNames(eclEngine_application $parent): array
    {
        return [];
    }

    public static function constructorHelper(eclEngine_application $me): void
    {

    }

    static function dispatch(eclEngine_document $document): void
    {

    }

    static function getMap()
    {
        return self::$map;
    }
}
