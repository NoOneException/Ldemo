<?php

namespace L\response;

class JsonData extends Response
{
    private $data;
    protected $headers = ['Content-Type' => 'application/json'];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    protected function onGetContent(): string
    {
        return json_encode(array_merge([
            'ok' => true,
            'servertime' => time(),
        ], $this->getData()));
    }

    public function getData(): array
    {
        return $this->data;
    }
}