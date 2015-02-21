<?php

namespace Application\Components\EventDispatcher;


use Application\Core\Interfaces\EventHandler as EventHandlerInterface;
use Application\Core\Lib\AppMethods;


class EventHandler extends AppMethods implements EventHandlerInterface{

    public function Notify($event, $args = null)
    {
    	$eventHandlers = \Get::Config('Observers.' . $event);

    	if(! $eventHandlers) {
    		return $this;
    	}

        $this->GetComponent('EventDispatcher')->Dispatch($event, $args, $eventHandlers);

        return $this;
    }
}