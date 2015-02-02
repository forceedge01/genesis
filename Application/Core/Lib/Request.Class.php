<?php

namespace Application\Core;



use Application\Core\Interfaces\Request as RequestInterface;

class Request extends AppMethods implements RequestInterface{

    public
            $post,
            $get,
            $server,
            $remoteIp,
            $scheme,
            $domain,
            $form;

    public function __construct() {

        $this->server = new Lib\Server();
        $this->post = $_POST;
        $this->get = $_GET;
        $this->remoteIp = $this->RemoteAddress();
        $this->scheme = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '');
        $this->domain = HOST;
        $this->form = new Form();
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function is($method) {
        if($this->server->get('REQUEST_METHOD') === strtoupper($method)) {
            return $this;
        }

        return false;
    }

    public function getCookie($Name){

        if(isset($_COOKIE[$Name]))
            return $_COOKIE[$Name];
        else
            return false;
    }

    public function hasCookie($Name){

        if(isset($_COOKIE[$Name]))
            return $this;
        else
            return false;
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

    public function getQueryStringValue($name) {
        return filter_input(INPUT_GET, $name);
    }

    public function getPostValue($name) {
        return filter_input(INPUT_POST, $name);
    }

    public function hasQueryStringValue($name) {
        return filter_has_var(INPUT_GET, $name);
    }

    public function hasPostValue($name) {
        return filter_has_var(INPUT_POST, $name);
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