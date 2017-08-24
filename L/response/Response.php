<?php

namespace L\response;

abstract class Response
{
    private $headers = [];
    protected $content;

    public final function setHeader(string $name, $val)
    {
        $this->headers[$name] = $val;
    }

    public final function setHeaders(array $headers = [])
    {
        $this->headers = $headers;
    }

    public function send()
    {
        $this->sendHeader();
        $str = ob_get_contents();
        ob_end_clean();
        if (\L::app()->env->isDebug()) {
            echo $str;
        }
        echo $this->getContent();
    }

    public final function getContent(): string
    {
        if ($this->content === null) {
            $this->content = $this->onGetContent();
        }
        return $this->content;
    }

    protected abstract function onGetContent(): string;

    private function sendHeader()
    {
        foreach ($this->headers as $name => $value) {
            header($name . ':' . $value);
        }
    }
}