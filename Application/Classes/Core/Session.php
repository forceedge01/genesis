<?php

namespace Application\Core;



class Session extends Request{

    public function Start(){

        session_start();

        return $this;
    }

    public function Save(){

        session_write_close();

        return $this;
    }

    public function Destroy(){

        session_destroy();

        return $this;
    }

    public function Set($name, $value){

        $_SESSION[$name] = $value;

        return $this;
    }

    public function Get($name){

        return $_SESSION[$name];

        return $this;
    }

    public function Status(){

        return session_status();
    }

    public function Remove($name){

        session_unset($name);

        return $this;
    }

    public function GetSessionHandler(){
        
        return new \SessionHandler();
    }

}