<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/22
 * Time: 17:17
 */

namespace L\interceptor;

use L\route\Request;

interface AppRunAfterInterceptor
{
    public function onAppRunAfter(Request $request): bool;
}