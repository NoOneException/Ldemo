<?php

namespace response;

use L\response\Response;

class StringData extends Response
{
    private $data;

    public function __construct(string $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    protected function onGetContent(): string
    {
        return $this->getData();
    }
}