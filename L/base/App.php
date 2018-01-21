<?php

namespace L\base;

use Exception;
use L\action\Action;
use L\action\RequestProcessor;
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
 * @property  string publicPath
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
        $this->publicPath = substr($_SERVER['DOCUMENT_ROOT'], 0, -1);
        $this->basePath = BASE_PATH;
    }

    public function __get($name)
    {
        if ($this->$name == null) {
            $componentProperty = $this->_components[$name];
            if ($class = $componentProperty['class']) {
                /** @var Component $component */
                $component = new $class();
                foreach ($componentProperty as $attr => $val) {
                    if ($attr != 'class') {
                        if (is_array($val)) {
                            if (!is_array($component->$attr)) {
                                $component->$attr = [];
                            }
                            $component->$attr = array_merge($component->$attr, $val);;
                        } else {
                            $component->$attr = $val;
                        }
                    }
                }
                $component->init();
                $this->$name = $component;
            }
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
            var_dump($e->getMessage());
            $this->handleException($e);
        }
    }

    private function main()
    {
        $this->init();
        $processor = $this->getRequestProcessor();
        $this->response = $processor->onProcess();
        $this->response->send();
    }

    private function init()
    {
        $actionClass = $this->url->getActionClass();
        if (file_exists(\Autoload::getClassFile($actionClass))) {
            $this->action = new $actionClass();
        } else {
            $controllerClass = $this->url->getControllerClass();
            if (!file_exists(\Autoload::getClassFile($controllerClass))) {
                throw new \ErrorException('The controller is not exist.');
            }
            $this->controller = new $controllerClass();
        }
    }

    private function getRequestProcessor(): RequestProcessor
    {
        if ($this->action) {
            return new RequestProcessor($this->action);
        }
        return new RequestProcessor($this->controller);
    }

    private function beforeRun()
    {
        if ($runBeforeInterceptors = $this->getRunBeforeInterceptors()) {
            foreach ($runBeforeInterceptors as $runBeforeInterceptor) {
                $interceptor = new $runBeforeInterceptor;
                if ($interceptor instanceof AppRunBeforeInterceptor && !$interceptor->onAppRunBefore($this->request)) {
                    exit;
                }
            }
        }
    }

    private function afterRun()
    {
        if ($runAfterInterceptors = $this->getRunAfterInterceptors()) {
            foreach ($runAfterInterceptors as $runAfterInterceptor) {
                $interceptor = new $runAfterInterceptor;
                if ($interceptor instanceof AppRunAfterInterceptor && !$interceptor->onAppRunAfter($this->request)) {
                    exit;
                }
            }
        }
    }

    private function doModuleStartInterceptors()
    {
        if ($moduleStartInterceptors = $this->getModuleStartInterceptors($this->url->getModule())) {
            foreach ($moduleStartInterceptors as $moduleStartInterceptor) {
                $interceptor = new $moduleStartInterceptor;
                if ($interceptor instanceof ModuleStartInterceptor && !$interceptor->onModuleStart($this->request)) {
                    exit;
                }
            }
        }
    }

    private function doModuleEndInterceptors()
    {
        if ($moduleEndInterceptors = $this->getModuleEndInterceptors($this->url->getModule())) {
            foreach ($moduleEndInterceptors as $moduleEndInterceptor) {
                $interceptor = new $moduleEndInterceptor;
                if ($interceptor instanceof ModuleEndInterceptor && !$interceptor->onModuleEnd($this->request, $this->response)) {
                    exit;
                }
            }
        }
    }

    /**
     * @return string[] class name of AppRunBeforeInterceptors
     */
    public function getRunBeforeInterceptors(): array
    {
        return $this->runBeforeInterceptors;
    }

    /**
     * @param string[] class name of AppRunBeforeInterceptors
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
                $interceptor = new $exceptionInterceptor;
                if ($interceptor instanceof AppExceptionInterceptor) {
                    $interceptor->onAppException($e);
                }
            }
        }
    }

}