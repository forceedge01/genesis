<?php

namespace Application\Core;


use Application\Core\Interfaces\EventDispatcher as EventDispatcherInterface;



abstract class EventDispatcher extends AppMethods implements EventDispatcherInterface{

    public static $observers;

    public static function Dispatch($event, $args, $bundle)
    {
        $events = self::GetNamespaceFromMultipleFiles(Loader::LoadEvents($bundle));
        self::$observers = (is_array(self::$observers)) ? array_merge(self::$observers, $events) : $events;
        $eventHandler = $event.'Handler';

        foreach(self::$observers as $observer)
        {
            if(!class_exists($observer))
                echo "Class $observer does not exist, cannot run event.", __CLASS__.'::'.__FUNCTION__, __LINE__;

            if(method_exists($observer, $eventHandler))
            {
                $event = new $observer();
                $event->$eventHandler($args);
            }
        }
    }
}