<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/7/11
 * Time: 18:36
 */

namespace L\log;

use L;

class Logger
{
    const DIR_APPLICATION = 'app';
    const DIR_ERROR = 'error';
    const DIR_DEBUG = 'debug';
    const DIR_SQL = 'sql';
    const DIR_FATAL = 'fatal';
    private $_logArr = [];
    public $file = [
        'app' => 'app',
        'debug' => 'debug',
        'sql' => 'sql',
    ];
    public $runtime = '/runtime';
    public $maxsize = 5120; //5M ,单位k

    public function init()
    {

    }

    public function save()
    {
        $contentArr = $this->getContent();
        if (empty($contentArr)) {
            return false;
        }
        $runtimePath = L::app()->basePath . $this->runtime;
        if (!is_dir($runtimePath)) {
            exit(L::t('tips_app_no_runtime_dir'));
        }
        if (!is_writable($runtimePath)) {
            exit(L::t('tips_app_runtime_dir_auth'));
        }
        $ext = '.log';
        $fileArr = array();
        $contentArrstr = array();
        foreach ($contentArr as $content) {
            $level = isset($this->file[$content['level']]) ? $this->file[$content['level']] : $content['level'];
            $fileArr[$level] = $level;
            if (!isset($contentArrstr[$level])) {
                $contentArrstr[$level] = '';
            }
            $contentArrstr[$level] .= $content['content'];

        }

        foreach ($fileArr as $filename) {
            $runtime = $runtimePath . '/' . date('Ymd') . '/' . $filename . '/';
            if (!is_dir($runtime)) {
                @mkdir($runtime, 0777, true);
                chmod($runtime, 0777);
            }
            $cmd = '';
            if (L::app()->env->isCmd()) {
                $cmd = 'cmd';
            }
            for ($i = 1; $i < 100000; $i++) {
                $file = $runtime . $i . $cmd . $ext;
                if (!(is_file($file) && (filesize($file) / 1024) >= $this->maxsize)) {
                    break;
                }
            }
            if (isset($contentArrstr[$filename])) {
                $handle = fopen($file, 'a');
                fwrite($handle, $contentArrstr[$filename]);
                fclose($handle);
            }

        }
        $this->_logArr = [];
        return true;
    }

    protected function getContent()
    {
        $content = [];
        foreach ($this->_logArr as $level => $logArr) {
            $logstr = date('Y-m-d H:i:s') . " [$level] \n";

            foreach ($logArr as $log) {
                $logstr .= ($log['catalog'] ? '[' . $log['catalog'] . ']  ' : '') . $log['msg'] . "\n";
            }

            $logstr .= "\nREQUEST_URI=  " . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
            if (!empty($_SERVER['argv'])) {
                $logstr .= "\nargv=  " . var_export($_SERVER['argv'], true);
            }
            $logstr .= "\n\nPOST " . L::t('data') . "：   " . var_export($_POST, true) . "\n\n" . '==========================================================================' . "\n\n";
            $content[] = [
                'level' => $level,
                'content' => $logstr,
            ];


        }
        return $content;

    }

    public function add($msg, $level, $catalog, $backtrace = 0)
    {
        if ($level != self::DIR_APPLICATION && $level != self::DIR_SQL && $level != self::DIR_FATAL) {
            $msg = is_string($msg) ? $msg : str_replace("\\'", "'", var_export($msg, true)) . "\n" . Cutil::getCallLine(debug_backtrace(), $backtrace);
        }
        if (!isset($this->_logArr[$level])) {
            $this->_logArr[$level] = array();
        }
        $this->_logArr[$level][] = array(
            'msg' => is_string($msg) ? $msg : str_replace("\\'", "'", var_export($msg, true)),
            'level' => $level,
            'catalog' => $catalog,
        );
        if (L::app()->env->isCmd()) {
            $this->save();
        }
        return $this;
    }
}