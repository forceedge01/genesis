<?php

namespace Application\Core\Lib;

class Server {

    public function get($name)
    {
        return filter_input(INPUT_SERVER, $name, FILTER_SANITIZE_STRING);
    }
}