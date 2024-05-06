<?php

class eclApp_admin_notFound extends eclApp
{
    public static $name = '-default';
    public static $staticContent = 'admin_notFound';

    public static function constructorHelper(eclEngine_application $me): void
    {
        $me->ignoreSubfolders = true;
    }

}
