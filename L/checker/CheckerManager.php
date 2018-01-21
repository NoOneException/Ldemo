<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/25
 * Time: 16:13
 */

namespace L\checker;


class CheckerManager
{
    public static function doCheck(array $checkConf)
    {
        if ($checkConf) {
            $checkerFactory = CheckerFactory::instance();
            foreach ($checkConf as $conf) {
                if (isset($conf[2]) && !in_array(\L::app()->url->getAction(), $conf[2])) {
                    continue;
                }
                $checker = $checkerFactory->getChecker($conf[0]);
                $checker->setArgs($conf[1])->onCheck(\L::app()->request);
            }
        }
    }
}