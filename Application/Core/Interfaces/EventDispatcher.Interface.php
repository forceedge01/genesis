<?php

namespace Application\Core\Interfaces;



interface EventDispatcher{

    public static function Dispatch($event, $args, $class);
}