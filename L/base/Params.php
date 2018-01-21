<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/10/9
 * Time: 10:50
 */

namespace L\base;


class Params implements Component
{
    private static $config = [];

    public function init()
    {
    }

    public function __get($name)
    {
        return self::$config[$name];
    }

    public function __set($name, $value)
    {
        self::$config[$name] = $value;
    }

    public function offsetExists($offset)
    {
        return isset(self::$config[$offset]);
    }
}