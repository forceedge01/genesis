<?php

namespace Application\Core;



use Application\Core\Interfaces\Request as RequestInterface;

class Request extends AppMethods implements RequestInterface{

    public
            $post,
            $get,
//            $method,
            $server,
//            $time,
//            $uri,
            $remoteIp,
//            $self,
            $scheme,
            $domain;

    public function __construct() {

        $this->server = new Lib\Server();
        $this->post = $_POST;
        $this->get = $_GET;
//        $this->method = $_SERVER['REQUEST_METHOD'];
//        $this->time = $_SERVER['REQUEST_TIME'];
//        $this->uri = $_SERVER['REQUEST_URI'];
        $this->remoteIp = $this->RemoteAddress();
//        $this->self = $_SERVER['PHP_SELF'];
        $this->scheme = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '');
        $this->domain = HOST;
    }

    public function GetDomain()
    {
        return $this->domain;
    }

    public function GetScheme()
    {
        return $this->scheme;
    }

    /**
     *
     * @return boolean
     * Returns true if the request is an ajax request
     */
    public function IsAjax(){

        if($this->server->get('HTTP_X_REQUESTED_WITH') == 'xmlhttprequest')
        {
            return $this;
        }

        return false;
    }

    /**
     * @param String The element to check (optional)
     * @return boolean
     * Returns true if the request is a post request
     */
    public function IsPost($key = null){

        if($this->server->get('REQUEST_METHOD') === 'POST') {

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

        if($this->server->get('REQUEST_METHOD') === 'GET') {

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

    public function RemoteAddress()
    {
        $ipaddress = '';

        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }

    public function IsProxiedRequest()
    {
        if($this->server->get('HTTP_X_FORWARDED_FOR') || $this->server->get('HTTP_CLIENT_IP'))
            return true;

        return false;
    }

    public function blockProxiedUsers()
    {
        if($this->IsProxiedRequest())
            die('Access denied for proxy users.');
    }

    public function IsLocal()
    {
        if($this->server->get('REMOTE_ADDR') == $this->server->get('SERVER_ADDR'))
            return true;

        return false;
    }

    public function GetStatus()
    {
        return $this->server->get('REDIRECT_STATUS');
    }
}