<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/24
 * Time: 14:51
 */

namespace L\interceptor;


interface DbConditionInterceptor
{
    public function onDbCondition();
}