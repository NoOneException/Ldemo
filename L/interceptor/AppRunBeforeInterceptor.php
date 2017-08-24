<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/22
 * Time: 17:15
 */

namespace L\interceptor;

use L\route\Request;

interface AppRunBeforeInterceptor
{
    public function onAppRunBefore(Request $request): bool;
}