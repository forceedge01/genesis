<?php

namespace Application\Core\Lib;


use Application\Core\Interfaces\EventHandler as EventHandlerInterface;



class EventHandler extends AppMethods implements EventHandlerInterface{

    public function Notify($event, $args = null)
    {
        $bundle = $this->GetBundleFromName($this->GetClassFromNameSpacedController(get_called_class()));
        $this->GetCoreObject('EventDispatcher')->Dispatch($event, $args, $bundle);

        return $this;
    }
}