<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/25
 * Time: 15:11
 */

namespace L\interceptor;


use L\route\Request;

interface ActionBeforeInterceptor
{
    public function onActionBefore(Request $request);
}