<?php

namespace Application\Core;



class Session extends Request{

    protected $User;

    public function Start($secure = false, $httponly = true){

        $session_name = 'secure_session_id37736'; // Set a custom session name

        ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies.
        $cookieParams = session_get_cookie_params(); // Gets current cookies params.
        session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
        session_name($session_name); // Sets the session name to the one set above.
        session_start(); // Start the php session
        session_regenerate_id(true);

        return $this;
    }

    public function Save(){

        session_write_close();

        return $this;
    }

    public function Destroy(){

        // Unset all session values
        $_SESSION = array();

        // get session parameters
        $params = session_get_cookie_params();

        // Delete the actual cookie.
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);

        // Destroy session
        session_destroy();

        return $this;
    }

    public function Set($name, $value){

        $_SESSION[$name] = $value;

        return $this;
    }

    public function IsSessionKeySet($Name){

        if(isset($_SESSION[$Name]))
            return $this;
        else
            return false;
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

    public function GetUsername()
    {
        return $this->Get('username');
    }

    public function GetLoginTime()
    {
        return $this->Get('login_time');
    }

    public function GetUser()
    {
        return $this->GetEntity('users:users')->FindBy(array(\Get::Config('Auth.DBTable.AuthColumnName') => $this->Get('username')));
    }
}