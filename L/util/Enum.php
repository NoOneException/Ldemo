<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/10/9
 * Time: 16:56
 */

namespace L\util;

abstract class Enum
{
    protected static $values = array();
    private static $objs = null;

    /**
     * @return $this
     */
    public static function init(): Enum
    {
        $class = get_called_class();
        if (!isset(self::$objs[$class])) {
            $static = new static();
            $static->initDatas();
            self::$objs[$class] = $static;
        }
        return self::$objs[$class];
    }

    public static function getValues():array
    {
        return self::init()->getAllValue();
    }

    public function getAllValue():array
    {
        $class = get_called_class();
        return self::$values[$class] ?? [];
    }

    public static function getValueByIndex($index, $defaultIndex = null)
    {
        return self::init()->getValueByKey($index, $defaultIndex);
    }

    public function getValueByKey($key, $defaultIndex = null)
    {
        $class = get_called_class();
        return self::$values[$class][$key] ?? self::$values[$class][$defaultIndex];
    }

    protected function add($index, $val)
    {
        $class = get_called_class();
        self::$values[$class][$index] = \L::t($val);
    }

    protected function addForList(array $list, string $index_name, string $value_name)
    {
        $class = get_called_class();
        self::$values[$class] = ArrayUtil::arrayColumn($list, $value_name, $index_name);
    }

    protected function addForArray(array $array)
    {
        $class = get_called_class();
        self::$values[$class] = $array;
    }

    abstract protected function initDatas();
}