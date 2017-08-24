<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/22
 * Time: 17:22
 */

namespace L\interceptor;

use L\route\Request;

interface ModuleStartInterceptor
{
    public function onModuleStart(Request $request): bool;
}