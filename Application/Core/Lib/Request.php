<?php

namespace Application\Core\Lib;

require_once 'Form.php';

use Application\Core\Interfaces\RequestInterface;
use Application\Core\Lib\Server;
use Application\Core\Lib\Form;

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

        $this->server = new Server();
        $this->post = $_POST;
        $this->get = $_GET;
        $this->remoteIp = $this->remoteAddress();
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

    public function isPost() {
	    if(isset($_POST) && $_POST) {
            return true;
        }

	    return false;
    }

    public function isGet() {
        if(isset($_GET) and $_GET) {
            return true;
        }

	    return false;
    }

    public function isAjax() {
    	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	    	return true;    
    	}

	    return false;
    }

    public function getQueryStringParam($key) {
	    return filter_input(INPUT_GET, $key); 
    }

    public function getPostParam($key) {
        return filter_input(INPUT_POST, $key); 
    }

    public function getCookie($Name){
        if(isset($_COOKIE[$Name]))
            return $_COOKIE[$Name];
        
        return false;
    }

    public function hasCookie($Name){
        if(isset($_COOKIE[$Name]))
            return $this;
       
        return false;
    }

    public function remoteAddress()
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

    public function isProxiedRequest()
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

    public function isLocal()
    {
        if($this->server->get('REMOTE_ADDR') == $this->server->get('SERVER_ADDR'))
            return true;

        return false;
    }

    public function getStatus()
    {
        return $this->server->get('REDIRECT_STATUS');
    }
}
