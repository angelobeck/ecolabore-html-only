<?php

class eclApp_admin extends eclApp
{
    public static $name = SYSTEM_ADMIN_URI;
    public static $map = ['admin_index', 'admin_notFound'];
    public static $control = 'admin_index';
}
