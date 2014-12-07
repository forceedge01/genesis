<?php

namespace Application\Core;



class Session extends Request{

    protected $User;

    public function Start($name = null)
    {
        session_name($name);

        if (session_status() == PHP_SESSION_NONE)
        {
            session_start();
        }

        return $this;
    }

    public function StartSecure($httpsSecure = false, $httponly = true)
    {
        $name = 'PHPGENESISSECURESESSID_37733627';
        $this->UseCookiesOnly();
        $this->SetSessionCookieParams(session_get_cookie_params(), $httpsSecure, $httponly);
        $this->Start($name);
        return $this;
    }

    public function SetSessionCookieParams($cookieParams, $secure, $httponly)
    {
        session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
    }

    public function UseCookiesOnly()
    {
        ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies.
    }

    public function Save(){

        session_write_close();

        return $this;
    }

    public function Destroy(){

        // Unset all session values
        $this->Clear();

        // get session parameters
        $params = session_get_cookie_params();

        // Delete the actual cookie.
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);

        // Destroy session
        session_destroy();

        return $this;
    }

    public function Clear()
    {
        $_SESSION = array();
        return $this;
    }

    public function RegenerateId($bool)
    {
        session_regenerate_id($bool);
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

    public function GetServerInfo($key)
    {
        return $_SERVER[$key];
    }

    public function GetBrowserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }
}