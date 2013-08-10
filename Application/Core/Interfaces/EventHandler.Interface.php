<?php

namespace Application\Core\Interfaces;



interface EventHandler{

    public function Attach($observer);

    public function Detach($observer);

    public function Notify($event, $args);
}