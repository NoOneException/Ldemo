<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/30
 * Time: 17:43
 */

namespace L\util;


class SysUtil
{
    public static function getCallLine(array $debugBacktraceArr, int $backtrace = 0): string
    {
        $log = "\n";
        foreach ($debugBacktraceArr as $i => $item) {
            if ($i > $backtrace) {
                continue;
            }
            $t = $item;
            if (!isset($t['file']))
                $t['file'] = 'unknown';
            if (!isset($t['line']))
                $t['line'] = 0;
            if (!isset($t['function']))
                $t['function'] = 'unknown';
            $log .= "{$t['file']}({$t['line']}): ";
            if (isset($t['object']) && is_object($t['object']))
                $log .= get_class($t['object']) . '->';
            $log .= "{$t['function']}()" . "\n";
            if (!empty($t['args']))
                $log .= "args:" . json_encode($t['args']) . "\n";
        }
        return $log;
    }

    public static function isWindows(): bool
    {
        return DIRECTORY_SEPARATOR == '\\';
    }
}