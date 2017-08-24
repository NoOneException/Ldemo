<?php

namespace L\route;

final class Url
{
    public $indexFile = '/index.php';
    private $module;
    private $action;
    private $controller;
    private $defaultAction = 'index';
    private $defaultController = 'index';
    private $defaultModule = 'admin';

    private static $_baseUrl;

    public function __construct()
    {
        list($this->module, $this->controller, $this->action) = $this->getDataByUri();
    }

    public function getBaseUrl(): string
    {
        if (self::$_baseUrl === null) {
            $fileName = $_SERVER['SCRIPT_FILENAME'];
            $documentRoot = $_SERVER['DOCUMENT_ROOT'];
            $scriptName = basename($fileName);
            $fileName = str_replace($documentRoot, '', $fileName);
            self::$_baseUrl = str_replace('/' . $scriptName, '', $fileName);
        }
        return self::$_baseUrl;
    }

    public function genurl(string $action_str = '', array $params = [])
    {
        list($action, $controller, $module) = $this->parseActionStr($action_str);
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
        if ($action_str != '') {
            if (preg_match('/^\/\w+$/', $action_str)) {
                $action_str .= '/' . $this->defaultAction;
            }
            $action_arr = explode('/', $action_str);
        }
        $count = count($action_arr);
        $action = $action_arr[$count - 1] ?? $this->action;
        $controller = $action_arr[$count - 2] ?? $this->controller;
        $module = $action_arr[$count - 3] ?? $this->module;
        return [$action, $controller, $module];
    }

    public function getActionClass(): string
    {
        return '\\' . implode('\\', ['module', $this->module, $this->controller, ucfirst($this->action)]) . 'Action';
    }

    public function getControllerClass(): string
    {
        return '\\' . implode('\\', ['module', $this->module, $this->controller, ucfirst($this->controller)]) . 'Controller';
    }

    private function getDataByUri(): array
    {
        $uri = substr($_SERVER['REDIRECT_URL'], strlen($this->getBaseUrl()) + 1);
        $path_arr = [];
        $uri == '' or $uri == '/' or $path_arr = explode('/', $uri);
        $path_arr[0] = $path_arr[0] ?? $this->defaultModule;
        $path_arr[1] = $path_arr[1] ?? $this->defaultController;
        $path_arr[2] = $path_arr[2] ?? $this->defaultAction;
        return $path_arr;
    }

    private function genUrlForPath(string $module, string $controller, string $action, array $params = []): string
    {
        $url = implode('/', [$this->getBaseUrl(), $module ?? $this->module, $controller ?? $this->controller, $action ?? $this->action]);
        empty($params) or $url .= '?' . http_build_query($params);
        return $url;
    }

}