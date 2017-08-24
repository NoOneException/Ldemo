<?php

namespace L\base;

final class Env
{
    public $debug = false;

    public function isDebug(): bool
    {
        return $this->debug;
    }
}