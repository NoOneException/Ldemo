<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/22
 * Time: 17:22
 */

namespace L\interceptor;

use L\response\Response;
use L\route\Request;

interface ModuleEndInterceptor
{
    public function onModuleEnd(Request $request, Response $response): bool;
}