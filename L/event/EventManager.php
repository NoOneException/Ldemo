<?php
/**
 * Created by PhpStorm.
 * User: Mr.Li
 * Date: 2017/10/9
 * Time: 12:21
 */

namespace L\event;


class EventManager
{
    private static $handlers = [];

    public static function addHandler($name, $handler, $modules = '_all')
    {
        self::$handlers[$name][] =  ['handler' => $handler,'groups' => $modules];
    }

    public static function trigger($name, &$data = null,Event $event = null)
    {
        if($event == null){
            $event = new Event();
        }
        $event->setType($name);
        if(isset(self::$handlers[$name]) && is_array(self::$handlers[$name])){
            foreach (self::$handlers[$name] as $handlerConf) {
                if($handlerConf['groups'] != '_all' && in_array(\L::app()->url->getModule(),explode(',',$handlerConf['groups']))){
                    continue;
                }
                $handler = $handlerConf['handler'];
                /**
                 * @var  EventHandler $handler
                 */
                $handler->onTrigger($data,$event);
            }
        }
    }
}