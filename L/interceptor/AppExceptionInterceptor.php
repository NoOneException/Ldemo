<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/22
 * Time: 17:19
 */

namespace L\interceptor;


interface AppExceptionInterceptor
{
    public function onAppException(\Exception $exception);
}