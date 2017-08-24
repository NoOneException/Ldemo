<?php

namespace L\base;

use L;

@session_start();

class Session
{
    public static function set($key, $val)
    {
        $key = self::getSessionKey($key);
        $_SESSION[$key] = $val;
    }

    public static function get($key, $default = null)
    {
        $key = self::getSessionKey($key);
        return $_SESSION[$key] ?? $default;
    }

    protected static function getSessionKey($key): string
    {
        return md5(L::app()->basePath) . '_' . L::app()->url->getModule() . '_' . $key;
    }

    public static function getAll(): array
    {
        $pre = self::getSessionKey('');
        $arr = [];
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, $pre) === 0) {
                $data_key = str_replace($pre, '', $key);
                $arr[$data_key] = $value;
            }
        }
        return $arr;
    }

    public static function destroy()
    {
        $pre = self::getSessionKey('');
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, $pre) === 0) {
                unset($_SESSION[$key]);
            }
        }
    }
}