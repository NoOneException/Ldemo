<?php

namespace L\route;

use L\base\Session;

final class Request
{
    private $params;

    public function __construct()
    {
        $this->params = array_merge((array)$_GET, (array)$_POST);
    }

    public function getParam(string $key, $default = null)
    {
        $val = $this->params[$key] ?? null;
        return $val === null ? $default : (is_string($val) ? trim($val) : $val);
    }

    public function getParamJson(string $key, array $default = []): array
    {
        $param = $this->getParam($key);
        $paramArr = $param && is_string($param) ? @json_decode($param, true) : null;
        return is_array($paramArr) ? $paramArr : $default;
    }

    public function setParam(string $key, $val): self
    {
        $this->params[$key] = $val;
        return $this;
    }

    public function getLastUrl(): string
    {
        $last_url = Session::get('__request_last_url');
        $url = $_SERVER['HTTP_REFERER'];
        if ($last_url != $url && strpos($url, $_SERVER['REQUEST_URI']) === false) {
            Session::set('__request_last_url', $url);
        }
        return (string)Session::get('__request_last_url');
    }
}