<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/25
 * Time: 15:30
 */

namespace L\checker;


class CheckerFactory
{
    private static $_instance;
    private $checkerMap = [];
    private $checkerConf = [
        'required' => 'RequiredChecker',
        'email' => 'EmailChecker'
    ];

    private function __construct()
    {
    }

    public static function instance(): self
    {
        if (self::$_instance === null) {
            self::$_instance = new static();
        }
        return self::$_instance;
    }

    public function getChecker(string $checkerName): ParamChecker
    {
        if (!isset($this->checkerMap[$checkerName])) {
            if (isset($this->checkerConf[$checkerName])) {
                $checkerClass = $this->checkerConf[$checkerName];
                $this->checkerMap[$checkerName] = new $checkerClass;
            } else {
                $this->checkerMap[$checkerName] = new $checkerName;
            }
        }
        return $this->checkerMap[$checkerName];
    }
}