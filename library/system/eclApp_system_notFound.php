<?php

class EclApp_system_notFound extends eclApp
{
    public static $name = '-default';
    public static $control = 'system_notFound';

    static function constructorHelper(eclEngine_application $me): void
    {
        $me->ignoreSubfolders = true;
    }

}
