<?php

class eclApp_admin_home extends eclApp
{
    public static $name = '-home';
    public static $staticContent = 'admin_home';

    public static function constructorHelper(eclEngine_application $me): void
    {
        $me->path = $me->parent->path;
    }

}
