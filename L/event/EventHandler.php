<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/10/9
 * Time: 12:21
 */

namespace L\event;


interface EventHandler
{
    public function onTrigger(array &$data, Event $event);
}