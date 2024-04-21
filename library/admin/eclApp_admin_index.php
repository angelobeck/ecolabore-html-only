<?php

class eclApp_admin_index extends eclApp
{
    public static $name = '-index';
    public static $control = 'admin_index';

    public static function constructorHelper(eclEngine_application $me): void
    {
        $me->path = $me->parent->path;
    }

}
