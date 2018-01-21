<?php

namespace L\action;

use L;
use L\response\Response;
use L\route\Request;

/**
 * Class Controller
 * @package L\action
 * @property  Request request
 *
 */
class Controller extends RequestPerformer
{

    public function run(): Response
    {
        $actionMethod = 'action' . ucfirst(L::app()->url->getAction());
        if (!method_exists($this, $actionMethod)) {
            throw new \ErrorException('The action is not exist.');
        }
        return $this->$actionMethod();
    }
}