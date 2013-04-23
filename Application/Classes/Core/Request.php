<?php

class Request extends Debugger{

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
    public function isAjax(){

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

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
    public function isPost($Element = null){

        if($_SERVER['REQUEST_METHOD'] == 'POST') {

            if(!empty($Element)){

                if(isset($_POST[$Element]))
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
    public function isGet($Element = null){

        if($_SERVER['REQUEST_METHOD'] == 'GET') {

            if(!empty($Element)){

                if(isset($_GET[$Element]))
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
    public function setCookie($Name, $Value , $time = 2592000){

        setcookie($Name, '', -(time() + 2592000));

        if(setcookie($Name, $Value, time() + $time, '/'))
             return $this;
        else
            return false;
    }

    public function getCookie($Name){
        
        if(isset($_COOKIE[$Name]))
            return $_COOKIE[$Name];
        else
            return false;
    }
    
    public function isCookie($Name){
        
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
    public function unsetCookie($Name){

        if(setcookie($Name, '', -(time() + 2592000)))
            return $this;
        else
            return false;

    }

    public function setSession($Name, $Value){

        $_SESSION[$Name] = $Value;

        return $this;
    }

    public function unsetSession($Name){

        unset($_SESSION[$Name]);

        return $this;
    }

    /**
     *
     * @param $string or $array $key
     * @return boolean
     */
    public function has($keys){

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
     * @return boolean
     */
    public function get($key){

        if(isset($_REQUEST[$key]))
            return $this;
        else
            return false;
    }
}