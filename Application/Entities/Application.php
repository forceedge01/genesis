<?php

class ApplicationEntity extends Debugger{

    protected
            $activeConnection;

    public function __construct(){

        $this->activeConnection = new Database();

    }
}