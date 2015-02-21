<?php

namespace Application\Components\EventDispatcher;


use Application\Core\Interfaces\EventDispatcher as EventDispatcherInterface;
use Application\Core\Lib\AppMethods;


class EventDispatcher extends AppMethods implements EventDispatcherInterface{

    public static $observers;

    public function Dispatch($event, $args, array $handlers)
    {
        self::$observers = $handlers;
        $eventHandler = $event.'Handler';

        foreach(self::$observers as $observer)
        {
            $handler = $this->ExpandHandler($observer);
            $this->loadEventFileFromHandler($handler);

            if(! class_exists($handler, false))
            {
                self::ThrowStaticError (
                        "Observer class '$handler' for event '$event' does not exist, check registered bundles.",
                        __CLASS__.'::'.__FUNCTION__, __FILE__, __LINE__
                        )->ThrowError();
            }

            if(method_exists($handler, $eventHandler))
            {
                self::InstantiateObject($handler)->$eventHandler($args);
            }
        }
    }

    private function loadEventFileFromHandler($handler) {
        // TODO: Not to include source maybe?
        \Application\Appkernal::getLoader()->LoadFileFromClass('\Source' . $handler);
    }

    private function ExpandHandler($bundledController) {
        list($bundle, $handler) = explode(':', $bundledController);

        if(! $bundle) {
            return '\Application\Struct\Events\\' . $handler;
        }

        return '\Bundles\\'. $bundle .'\Events\\' . $handler;
    }
}