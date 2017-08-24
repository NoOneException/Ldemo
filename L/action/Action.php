<?php

namespace L\action;

use L;
use L\response\Response;

abstract class Action
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

    abstract public function run(): Response;
}