<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/8/25
 * Time: 17:46
 */

namespace L\i18n;


class Translator
{
    private static $_dictionary;

    public static function t(string $str, array $params = [], $language = null)
    {
        $language = $language ?? \L::app()->env->language;
        if (!isset(self::$_dictionary[$language])) {
            $lFile = L_PATH . '/i18n/' . $language . '.php';
            $file = \L::app()->basePath . '/i18n/' . $language . '.php';
            $lDictionary = [];
            $dictionary = [];
            if (file_exists($file)) {
                $dictionary = include $file;
            }
            if (file_exists($lFile)) {
                $lDictionary = include $lFile;
            }
            self::$_dictionary[$language] = array_merge((array)$lDictionary, (array)$dictionary);
        }
        $str = self::$_dictionary[$language][$str] ?? $str;
        if ($params) {
            foreach ($params as $k => $v) {
                $str = str_replace('{' . $k . '}', $v, $str);
            }
        }
        return $str;
    }
}