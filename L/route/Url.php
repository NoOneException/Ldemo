<?php

namespace L\route;

use L\base\Component;

final class Url implements Component
{
    public $indexFile = '/index.php';
    private $module;
    private $action;
    private $controller;
    public $defaultAction = 'index';
    public $defaultController = 'index';
    public $defaultModule = 'admin';
    public $singleModule = false;
    public $route = [];

    private static $_baseUrl;

    public function init()
    {
        list($this->module, $this->controller, $this->action) = $this->getDataByUri();
    }

    public function getBaseUrl(): string
    {
        if (self::$_baseUrl === null) {
            $fileName = $_SERVER['SCRIPT_FILENAME'];
            $documentRoot = $_SERVER['DOCUMENT_ROOT'];
            $scriptName = basename($fileName);
            $fileName = substr($fileName, strlen($documentRoot));
            self::$_baseUrl = substr($fileName, 0, -(strlen($scriptName)));
        }
        return self::$_baseUrl;
    }

    public function genurl(string $actionStr = '', array $params = [])
    {
        list($action, $controller, $module) = $this->parseActionStr($actionStr);
        $url = $this->genUrlForPath($module, $controller, $action, $params);
        return $url;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function parseActionStr(string $action_str): array
    {
        $action_arr = [];
        if ($action_str !== '') {
            if (preg_match('/^\/\w+$/', $action_str)) {
                $action_str .= '/' . $this->defaultAction;
            }
            $action_arr = array_values(array_filter(explode('/', $action_str)));
        }
        $length = count($action_arr);
        $action = $action_arr[$length - 1] ?? $this->action;
        $controller = $action_arr[$length - 2] ?? $this->controller;
        $module = $action_arr[$length - 3] ?? $this->module;
        return [$action, $controller, $module];
    }

    public function getActionClass(): string
    {
        return implode('\\', ['', 'module', $this->module, $this->controller, ucfirst($this->action)]) . 'Action';
    }

    public function getControllerClass(): string
    {
        return implode('\\', ['', 'module', $this->module, $this->controller, ucfirst($this->controller)]) . 'Controller';
    }

    private function getDataByUri(): array
    {
        $uri = substr($_SERVER['REDIRECT_URL'], strlen($this->getBaseUrl()));
        $pathArr = [];
        if ($uri !== '') {
            $uri = $this->route[$uri] ?? $uri;
            if ($uri !== '/') {
                $tempPathArr = explode('/', strtolower($uri));
                foreach ($tempPathArr as $item) {
                    if ($item !== '') {
                        if (strpos($item, '-') !== false) {
                            $item = lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $item))));
                        }
                        $pathArr[] = $item;
                    }
                }
            }
        }
        return $this->singleModule ?
            [$this->defaultModule, $pathArr[0] ?? $this->defaultController, $pathArr[1] ?? $this->defaultAction] :
            [$pathArr[0] ?? $this->defaultModule, $pathArr[1] ?? $this->defaultController, $pathArr[2] ?? $this->defaultAction];
    }

    private function genUrlForPath(string $module, string $controller, string $action, array $params = []): string
    {
        $pathArr = $this->singleModule ?
            [$this->getBaseUrl(), $controller ?? $this->controller, $action ?? $this->action] :
            [$this->getBaseUrl(), $module ?? $this->module, $controller ?? $this->controller, $action ?? $this->action];
        $url = implode('/', $pathArr);
        empty($params) or $url .= '?' . http_build_query($params);
        return $url;
    }

}