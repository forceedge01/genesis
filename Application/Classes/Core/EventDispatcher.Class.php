<?php

namespace Application\Core;


use Application\Core\Interfaces\EventDispatcher as EventDispatcherInterface;



abstract class EventDispatcher extends AppMethods implements EventDispatcherInterface{

    public static $observers;

    public static function Dispatch($event, $args, $class)
    {
        Loader::LoadEvents(get_class($class));
        $eventHandler = $event.'Handler';

        foreach(self::$observers as $observer)
        {
            if(!class_exists($observer))
                $this->SetErrorArgs ("Class $observer does not exist, cannot run event.", '', __LINE__)->ThrowError();

            if(method_exists($observer, $eventHandler))
            {
                $event = new $observer;
                $event->$eventHandler($args);
            }
        }
    }
}