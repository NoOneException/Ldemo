<?php

namespace L\base;

final class Env implements Component
{
    const ENV_WEB = 'web';
    const ENV_API = 'api';
    const ENV_CMD = 'cmd';

    public $debug = false;
    public $language = 'zh-cn';
    public $api = 'api';
    public $cmd = 'cmd';
    public $web = 'admin,index';
    private $currentEnv;

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function init()
    {
        $module = \L::app()->url->getModule();
        if (in_array($module, explode(',', $this->api))) {
            $this->currentEnv = self::ENV_API;
        } elseif (in_array($module, explode(',', $this->cmd))) {
            $this->currentEnv = self::ENV_CMD;
        } elseif (in_array($module, explode(',', $this->web))) {
            $this->currentEnv = self::ENV_WEB;
        } else {
            throw new \ErrorException('404');
        }
    }

    public function isApi()
    {
        return $this->getCurrentEnv() == self::ENV_API;
    }

    public function isCmd()
    {
        return $this->getCurrentEnv() == self::ENV_CMD;
    }

    public function isWeb()
    {
        return $this->getCurrentEnv() == self::ENV_WEB;
    }

    /**
     * @return mixed
     */
    public function getCurrentEnv()
    {
        return $this->currentEnv;
    }
}