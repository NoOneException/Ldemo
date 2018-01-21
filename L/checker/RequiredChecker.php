<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/25
 * Time: 16:13
 */

namespace L\checker;

use L\route\Request;

class RequiredChecker extends ParamChecker
{
    public function onCheck(Request $request)
    {
        if ($args = $this->getArgs()) {
            foreach ($args as $arg) {
                if ($request->getParam($arg, '') === '') {
                    throw new \ErrorException('');
                }
            }
        }
    }

}