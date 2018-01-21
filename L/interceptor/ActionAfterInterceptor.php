<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/25
 * Time: 15:11
 */

namespace L\interceptor;


use L\response\Response;

interface ActionAfterInterceptor
{
    public function onActionAfter(Response $response);
}