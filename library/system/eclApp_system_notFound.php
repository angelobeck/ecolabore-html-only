<?php

class EclApp_system_notFound extends eclApp
{
    public static $name = '-default';

    static function constructorHelper(eclEngine_application $me): void
    {
        global $store;
        $me->data = $store->control->open('system_notFound');
        $me->ignoreSubfolders = true;
    }

}
