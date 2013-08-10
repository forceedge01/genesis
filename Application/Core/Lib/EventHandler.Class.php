<?php

namespace Application\Core;


use Application\Core\Interfaces\EventHandler as EventHandlerInterface;



abstract class EventHandler extends AppMethods implements EventHandlerInterface{

    public function Attach($observer)
    {
        EventDispatcher::$observers[] = (is_object($observer)) ? get_class($observer) : $observer;
    }

    public function Detach($observer)
    {
        $unset = (is_object($observer)) ? get_class($observer) : $observer;
        $this->observers = $this->Variable($this->observers)->RemoveValue($unset)->GetVariableResult();

        return $this;
    }

    public function Notify($event, $args = null)
    {
        $bundle = $this->GetBundleFromName($this->GetClassFromNameSpacedController(get_called_class()));
        EventDispatcher::Dispatch($event, $args, $bundle);
    }
}