<?php

namespace Application\Core;



class Request extends AppMethods{

    public
            $post,
            $get,
            $method,
            $server,
            $time,
            $uri,
            $remoteIp,
            $self;

    public function __construct() {

        $this->post = $_POST;
        $this->get = $_GET;
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->time = $_SERVER['REQUEST_TIME'];
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->remoteIp = $_SERVER['REMOTE_ADDR'];
        $this->self = $_SERVER['PHP_SELF'];
        $this->server = $_SERVER;
    }

    /**
     *
     * @return boolean
     * Returns true if the request is an ajax request
     */
    public function IsAjax(){

        if($this->Variable($_SERVER['HTTP_X_REQUESTED_WITH'])->IsNotEmpty()->ToLower()->Equals('xmlhttprequest')) {

            return $this;
        }
        else{

            return false;
        }

    }

    /**
     * @param String The element to check (optional)
     * @return boolean
     * Returns true if the request is a post request
     */
    public function IsPost($key = null){

        if($this->Variable($_SERVER['REQUEST_METHOD'])->Equals('POST')) {

            if(!empty($key)){

                if(isset($_POST[$key]))
                    return $this;
                else
                    return false;

            }
            else
                return $this;
        }
        else{

            return false;
        }
    }

    /**
     * @param String The element to check (optional)
     * @return boolean
     * Returns true if the request is an get request
     */
    public function IsGet($key = null){

        if($this->Variable($_SERVER['REQUEST_METHOD'])->Equals('GET')) {

            if(!empty($key)){

                if(isset($_GET[$key]))
                    return $this;
                else
                    return false;

            }
            else
                return $this;
        }
        else{

            return false;
        }
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

    public function SetSession($Name, $Value){

        $_SESSION[$Name] = $Value;

        return $this;
    }

    public function UnsetSession($Name){

        unset($_SESSION[$Name]);

        return $this;
    }

    /**
     *
     * @param $string or $array $key
     * @return boolean
     */
    public function HasKeys($keys){

        if(is_array($keys)){

            foreach($keys as $key){

                if(!isset($_REQUEST[$key]))
                    return false;
            }

            return $this;
        }
        else{

            if(isset($_REQUEST[$key]))
               return $this;
            else
               return false;
        }
    }

    /**
     *
     * @param $string or $array $key
     * @return form value, post or get does not matter
     */
    public function Get($key){

        if(isset($_REQUEST[$key]))
            return $_REQUEST[$key];
        else
            return false;
    }
    
    public function GetServerInfo($key){
        
        if(isset($_SERVER[$key]))
            return $_SERVER[$key];
        else
            return false;
    }
    
    public function PostParams()
    {
        return $_POST;
    }
    
    public function GetParams()
    {
        return $_GET;
    }
    
    public function RequestParams()
    {
        return $_REQUEST;
    }
}