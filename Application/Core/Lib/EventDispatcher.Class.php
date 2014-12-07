<?php

namespace Application\Core;


use Application\Core\Interfaces\EventDispatcher as EventDispatcherInterface;



class EventDispatcher extends AppMethods implements EventDispatcherInterface{

    public static $observers;

    public function Dispatch($event, $args, $bundle)
    {
        self::$observers = $this->GetObservers($bundle);
        $eventHandler = $event.'Handler';

        foreach(self::$observers as $observer)
        {
            if(! class_exists($observer, false))
            {
                self::ThrowStaticError (
                        "Class $observer for bundle '$bundle' does not exist, check registered bundles.",
                        __CLASS__.'::'.__FUNCTION__, __FILE__, __LINE__
                        )->ThrowError();
            }

            if(method_exists($observer, $eventHandler))
            {
                self::InstantiateObject($observer)->$eventHandler($args);
            }
        }
    }

    public function GetObservers($bundle)
    {
        // Get Events from event files in events folder of the bundle
        $events = self::GetNamespaceFromMultipleFiles(\Application\AppKernal::getLoader()->LoadEvents($bundle));

        // Include observers from config bundle file
        $configObservers = \Get::Config($bundle.'.Observers');

        if(is_array($configObservers))
        {
            // Merge event observers
            $events = array_merge($events, $configObservers);
        }

        // merge and return results
        return (is_array(self::$observers)) ? array_merge(self::$observers, $events) : $events;
    }
}