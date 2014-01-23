<?php

namespace Application\Core;


use Application\Core\Interfaces\EventDispatcher as EventDispatcherInterface;



abstract class EventDispatcher extends AppMethods implements EventDispatcherInterface{

    public static $observers;

    public static function Dispatch($event, $args, $bundle)
    {
        self::$observers = self::GetObservers($bundle);
        $eventHandler = $event.'Handler';

        foreach(self::$observers as $observer)
        {
            if(!class_exists($observer, false))
                $this->SetErrorArgs ("Class $observer does not exist, cannot run event.", __CLASS__.'::'.__FUNCTION__, __FILE__, __LINE__)->ThrowError();

            if(method_exists($observer, $eventHandler))
            {
                $this->InstantiateObject($observer)->$eventHandler($args);
            }
        }
    }

    public static function GetObservers($bundle)
    {
        // Get Events from event files in events folder of the bundle
        $events = self::GetNamespaceFromMultipleFiles(Loader::LoadEvents($bundle));

        // Include observers from config bundle file
        $configObservers = \Get::Config($bundle.'.Observers');

        // Merge event observers
        $allEvents = array_merge($events, $configObservers);

        // merge and return results
        return (is_array(self::$observers)) ? array_merge(self::$observers, $allEvents) : $allEvents;
    }
}