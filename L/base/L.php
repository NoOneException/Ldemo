<?php

use L\base\App;

final class L
{
    private static $_app;

    public static function createApp(array $config): APP
    {
        if (!isset(self::$_app)) {
            self::$_app = new App($config);
        }
        return self::$_app;
    }

    public static function app(): App
    {
        if (!isset(self::$_app)) {
            throw new ErrorException('app未创建');
        }
        return self::$_app;
    }
}