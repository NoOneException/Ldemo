<?php

namespace L\response;

use Closure;
use L;
use ReflectionClass;

class RenderData extends Response
{
    private $view;
    private $isLayout;
    private $layout;
    private $url;
    private $data;

    public function __construct(array $data = [], string $view = '', bool $isLayout = true, $layout = null)
    {
        $this->data = $data;
        $this->view = $view;
        $this->isLayout = $isLayout;
        $this->layout = $layout;
        $this->url = L::app()->url;
    }

    protected function onGetContent(): string
    {
        return $this->render();
    }

    private function getViewDir(string $className): string
    {
        $func = new ReflectionClass($className);
        $pathInfo = pathinfo($func->getFileName());
        return $pathInfo['dirname'];
    }

    private function render()
    {
        $viewfile = $this->getView();
        if (!is_file($viewfile)) {
        }
        $layoutfile = '';
        if ($this->isLayout) {
            $layoutfile = $this->getLayout();
            if (!is_file($layoutfile)) {

            }
        }
        $render = function ($__data, $___view, $___isLayout, $___layoutFile) {
            extract($__data);
            $________c = ob_get_contents();
            ob_end_clean();
            ob_start();
            include $___view;
            $content = ob_get_contents();
            ob_end_clean();
            ob_start();
            echo $________c;
            if ($___isLayout) {
                include $___layoutFile;
                $content = ob_get_contents();
                ob_end_clean();
            }
            return $content;
        };
        $runObj = isset(L::app()->action) ? L::app()->action : L::app()->controller;
        $render = Closure::bind($render, $runObj, get_class($runObj));
        return $render($this->getData(), $viewfile, $this->isLayout, $layoutfile);
    }

    private function getView(): string
    {
        $runObj = L::app()->action ?? L::app()->controller;
        $viewDir = $this->getViewDir(get_class($runObj));
        return $viewDir . '/view/' . $this->url->getAction() . '.php';
    }

    private function getLayout(): string
    {
        $layout = $this->layout ?? $this->url->getModule();
        $layoutFile = L::app()->basePath . '/layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)){
            throw new \ErrorException('layout is not exits.');
        }
        return $layoutFile;
    }

    public function getData(): array
    {
        return $this->data;
    }

}