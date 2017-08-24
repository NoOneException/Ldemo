<?php

namespace L\response;

class NullData extends Response
{
    protected function onGetContent(): string
    {
        return '';
    }
}