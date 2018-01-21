<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/30
 * Time: 16:49
 */

namespace L\db\model;

use L\db\DBCondition;

abstract class ConditionModel extends DBCondition
{

    private static $_models = [];

    /**
     * @param string $table
     * @param bool $needTablePrefix
     * @return static
     */
    final public static function model(string $table = '',bool $needTablePrefix = true)
    {
        $class = get_called_class();
        if (!self::$_models[$class]) {
            self::$_models[$class] = new static();
        }
        return (self::$_models[$class])->table($table, $needTablePrefix);
    }

    final public function exec()
    {
        $res = $this->execute();
        $this->init();
        return $res;
    }

    abstract protected function execute();
}