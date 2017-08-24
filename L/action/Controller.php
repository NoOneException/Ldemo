<?php

namespace L\action;

use L;

class Controller
{
    public $request;
    public $baseUrl;
    public $lastUrl;

    public function __construct()
    {
        $this->init();
    }

    protected function init()
    {
        $this->baseUrl = L::app()->url->getBaseUrl();
        $this->request = L::app()->request;
        $this->lastUrl = $this->request->getLastUrl();
    }
}