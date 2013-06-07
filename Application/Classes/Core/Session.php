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
    
    

    /**
     *
     * @param type $Name - name of the cookie you want to setup
     * @param type $Value - Value of the cookie your setting up
     * @param type $time - Expiration time, has to be in seconds.
     * @return boolean
     * Returns true on successful cookie setup.
     */
    public function SetCookie($Name, $Value , $time = 2592000){

        setcookie($Name, '', -(time() + 2592000));

        if(setcookie($Name, $Value, time() + $time, '/'))
             return $this;
        else
            return false;
    }

    public function GetCookie($Name){

        if(isset($_COOKIE[$Name]))
            return $_COOKIE[$Name];
        else
            return false;
    }

    public function IsCookie($Name){

        if(isset($_COOKIE[$Name]))
            return $this;
        else
            return false;
    }

    /**
     *
     * @param type $Name - name of the cookie you want to setup
     * @return boolean
     * Returns true on successful cookie unset.
     */
    public function UnsetCookie($Name){

        if(setcookie($Name, '', -(time() + 2592000)))
            return $this;
        else
            return false;

    }
    
    public function RemoveCookie($Name)
    {
        $this ->UnsetCookie($Name);
    }

}