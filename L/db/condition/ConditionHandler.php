<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/29
 * Time: 16:19
 */

namespace L\db\condition;

use L\db\DBCondition;
use L\route\Request;

interface ConditionHandler
{
    public function onHandle(DBCondition $dbCondition, Request $request);
}