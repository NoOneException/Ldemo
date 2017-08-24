<?php

namespace L\base;

use Exception;
use L\action\Action;
use L\action\Controller;
use L\db\DBConnect;
use L\interceptor\AppExceptionInterceptor;
use L\interceptor\AppRunAfterInterceptor;
use L\interceptor\AppRunBeforeInterceptor;
use L\interceptor\ModuleEndInterceptor;
use L\interceptor\ModuleStartInterceptor;
use L\response\Response;
use L\route\Request;
use L\route\Url;

/**
 * @property  Url url
 * @property  Request request
 * @property  Env env
 * @property  DBConnect db
 * @property  string basePath
 * @property  Action action
 * @property  Controller controller
 * @property  Response response
 */
final class App
{
    private $_components = [
        'url' => ['class' => '\L\route\Url'],
        'request' => ['class' => '\L\route\Request'],
        'env' => ['class' => '\L\base\Env'],
        'db' => ['class' => '\L\db\DBConnect'],
    ];

    private $runBeforeInterceptors = [];
    private $runAfterInterceptors = [];
    private $exceptionInterceptors = [];
    private $moduleStartInterceptors = [];
    private $moduleEndInterceptors = [];

    public function __construct(array $config)
    {
        $this->_components = array_merge_recursive($this->_components, $config);
        $this->basePath = PUBLIC_PATH . '/../protected';
    }

    public function __get($name)
    {
        if ($this->$name == null) {
            $componentProperty = $this->_components[$name];
            $class = $componentProperty['class'];
            $obj = new $class();
            foreach ($componentProperty as $attr => $val) {
                if ($attr != 'class') {
                    if (is_array($val)) {
                        if (!is_array($obj->$attr)) {
                            $obj->$attr = [];
                        }
                        $obj->$attr = array_merge($obj->$attr, $val);;
                    } else {
                        $obj->$attr = $val;
                    }
                }
            }
            $this->$name = $obj;
        }
        return $this->$name;
    }

    public function run()
    {
        try {
            $this->beforeRun();
            $this->doModuleStartInterceptors();
            $this->main();
            $this->doModuleEndInterceptors();
            $this->afterRun();
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    private function main()
    {
        $actionClass = $this->url->getActionClass();
        $filename = \Autoload::getClassFile($actionClass);
        if (file_exists($filename)) {
            $this->action = new $actionClass();
            $this->response = $this->action->run();
        } else {
            $controllerClass = $this->url->getControllerClass();
            if (!file_exists(\Autoload::getClassFile($controllerClass))) {
                throw new \ErrorException('The controller is not exist.');
            }
            $this->controller = new $controllerClass();
            $actionMethod = $this->url->getAction() . 'Action';
            if (!method_exists($this->controller, $actionMethod)) {
                throw new \ErrorException('The action is not exist.');
            }
            $this->response = $this->controller->$actionMethod();
        }
        $this->response->send();
    }

    private function beforeRun()
    {
        if ($runBeforeInterceptors = $this->getRunBeforeInterceptors()) {
            foreach ($runBeforeInterceptors as $runBeforeInterceptor) {
                /** @var AppRunBeforeInterceptor $interceptor */
                $interceptor = new $runBeforeInterceptor;
                if (!$interceptor->onAppRunBefore($this->request)) {
                    exit;
                }
            }
        }
    }

    private function afterRun()
    {
        if ($runAfterInterceptors = $this->getRunAfterInterceptors()) {
            foreach ($runAfterInterceptors as $runAfterInterceptor) {
                /** @var AppRunAfterInterceptor $interceptor */
                $interceptor = new $runAfterInterceptor;
                if (!$interceptor->onAppRunAfter($this->request)) {
                    exit;
                }
            }
        }
    }

    private function doModuleStartInterceptors()
    {
        if ($moduleStartInterceptors = $this->getModuleStartInterceptors($this->url->getModule())) {
            foreach ($moduleStartInterceptors as $moduleStartInterceptor) {
                /** @var ModuleStartInterceptor $interceptor */
                $interceptor = new $moduleStartInterceptor;
                if (!$interceptor->onModuleStart($this->request)) {
                    exit;
                }
            }
        }
    }

    private function doModuleEndInterceptors()
    {
        if ($moduleEndInterceptors = $this->getModuleEndInterceptors($this->url->getModule())) {
            foreach ($moduleEndInterceptors as $moduleEndInterceptor) {
                /** @var ModuleEndInterceptor $interceptor */
                $interceptor = new $moduleEndInterceptor;
                if (!$interceptor->onModuleEnd($this->request, $this->response)) {
                    exit;
                }
            }
        }
    }

    /**
     * @return array class name of AppRunBeforeInterceptors
     */
    public function getRunBeforeInterceptors(): array
    {
        return $this->runBeforeInterceptors;
    }

    /**
     * @param array $runBeforeInterceptorClassNames
     */
    public function addRunBeforeInterceptors(array $runBeforeInterceptorClassNames)
    {
        $this->runBeforeInterceptors = array_merge($this->runBeforeInterceptors, $runBeforeInterceptorClassNames);
    }

    /**
     * @return array
     */
    public function getRunAfterInterceptors(): array
    {
        return $this->runAfterInterceptors;
    }

    /**
     * @param array $runAfterInterceptorClassNames
     */
    public function addRunAfterInterceptors(array $runAfterInterceptorClassNames)
    {
        $this->runAfterInterceptors = array_merge($this->runAfterInterceptors, $runAfterInterceptorClassNames);
    }

    /**
     * @return array
     */
    public function getExceptionInterceptors(): array
    {
        return $this->exceptionInterceptors;
    }

    /**
     * @param array $exceptionInterceptorClassNames
     */
    public function addExceptionInterceptors(array $exceptionInterceptorClassNames)
    {
        $this->exceptionInterceptors = array_merge($this->exceptionInterceptors, $exceptionInterceptorClassNames);
    }


    /**
     * @param string $module
     * @return array class name of ModuleStartInterceptors
     */
    public function getModuleStartInterceptors(string $module): array
    {
        return (array)$this->moduleStartInterceptors[$module];
    }

    /**
     * @param string $module
     * @param array $moduleStartInterceptorClassNames
     */
    public function addModuleStartInterceptor(string $module, array $moduleStartInterceptorClassNames)
    {
        $this->moduleStartInterceptors[$module] = array_merge($this->moduleStartInterceptors[$module], $moduleStartInterceptorClassNames);
    }

    /**
     * @param string $module
     * @return array class name of ModuleEndInterceptors
     */
    public function getModuleEndInterceptors(string $module): array
    {
        return (array)$this->moduleEndInterceptors[$module];
    }

    /**
     * @param string $module
     * @param array $moduleEndInterceptorClassNames
     */
    public function addModuleEndInterceptor(string $module, array $moduleEndInterceptorClassNames)
    {
        $this->moduleEndInterceptors[$module] = array_merge($this->moduleEndInterceptors[$module], $moduleEndInterceptorClassNames);
    }

    private function handleException($e)
    {
        if ($exceptionInterceptors = $this->getExceptionInterceptors()) {
            foreach ($exceptionInterceptors as $exceptionInterceptor) {
                /** @var AppExceptionInterceptor $interceptor */
                $interceptor = new $exceptionInterceptor;
                $interceptor->onAppException($e);
            }
        }
    }

}