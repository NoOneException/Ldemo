<?php

namespace L\response;

abstract class Response
{
    protected $headers = [];
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

    public function getHeaders() :array
    {
        return $this->headers;
    }

    private function sendHeader()
    {
        $headers = $this->getHeaders();
        foreach ($headers as $name => $value) {
            if (is_array($value) && $value) {
                foreach ($value as $item) {
                    header($name . ':' . $item);
                }
            } else {
                header($name . ':' . $value);
            }
        }
    }
}