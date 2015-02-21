<?php

namespace Application\Core\Interfaces;



interface EventDispatcher{

    public function Dispatch($event, $args, array $handlers);
}