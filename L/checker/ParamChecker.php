<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/25
 * Time: 15:17
 */

namespace L\checker;

use L\route\Request;

abstract class ParamChecker
{
    protected $args;

    public function setArgs($args)
    {
        $this->args = $args;
        return $this;
    }

    public function getArgs()
    {
        return $this->args;
    }

    abstract public function onCheck(Request $request);

}
