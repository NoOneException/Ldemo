<?php

namespace response;

use L\response\Response;

class RedirectData extends Response
{

    public function __construct(string $action = '', array $params = [])
    {
        if (strpos($action, 'http') === 0) {
            $link = $action;
        } else {
            $link = \L::app()->url->genurl($action, $params);
        }
        $this->setHeader('location', $link);
    }

    protected function onGetContent(): string
    {
        return '';
    }
}