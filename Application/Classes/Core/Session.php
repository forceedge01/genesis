<?php

namespace Application\Core;



class Session extends Request{

    public function start(){

        session_start();

        return $this;
    }

    public function save(){

        session_write_close();

        return $this;
    }

    public function destroy(){

        session_destroy();

        return $this;
    }

    public function set($name, $value){

        $_SESSION[$name] = $value;

        return $this;
    }

    public function get($name){

        return $_SESSION[$name];

        return $this;
    }

    public function status(){

        return session_status();
    }

    public function remove($name){

        session_unset($name);

        return $this;
    }


}