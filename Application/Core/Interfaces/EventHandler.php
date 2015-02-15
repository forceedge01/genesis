<?php

namespace Application\Core\Interfaces;



interface EventHandler{

    public function Notify($event, $args);
}