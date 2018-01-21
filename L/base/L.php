<?php

use L\base\App;
use L\i18n\Translator;

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

    public static function t(string $str, array $params = [], $language = null)
    {
        return Translator::t($str,$params,$language ?? self::app()->env->language);
    }
}