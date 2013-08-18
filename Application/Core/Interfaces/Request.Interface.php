<?php

namespace Application\Interfaces;



interface Request{

    /**
     *
     * @return boolean
     * Returns true if the request is an ajax request
     */
    public function isAjax();

    /**
     * @param String The element to check (optional)
     * @return boolean
     * Returns true if the request is a post request
     */
    public function IsPost($key = null);

    /**
     * @param String The element to check (optional)
     * @return boolean
     * Returns true if the request is an get request
     */
    public function IsGet($key = null);

    /**
     *
     * @param type $Name - name of the cookie you want to setup
     * @param type $Value - Value of the cookie your setting up
     * @param type $time - Expiration time, has to be in seconds.
     * @return boolean
     * Returns true on successful cookie setup.
     */
    public function SetCookie($Name, $Value , $time = 2592000);

    /**
     * @param string $Name
     * @return Gets a cookie value
     */
    public function GetCookie($Name);

    /**
     * @param string $Name
     * @return Checks if a cookie exists
     */
    public function IsCookie($Name);


    /**
     *
     * @param type $Name - name of the cookie you want to setup
     * @return boolean
     * Returns true on successful cookie unset.
     */
    public function UnsetCookie($Name);

    /**
     * @param string $Name
     * @return void
     * Unsets a cookie
     */
    public function RemoveCookie($Name);

    /**
     * @param string $Name
     * @param string $Value
     * @return boolean
     * Sets a session variable
     */
    public function SetSession($Name, $Value);

    /**
     * @param string $Name
     * @return void
     * Unsets a session variable
     */
    public function UnsetSession($Name);

    /**
     *
     * @param $string or $array $key
     * @return boolean
     */
    public function HasKeys($keys);

    /**
     *
     * @param $string or $array $key
     * @return form value, post or get does not matter
     */
    public function Get($key);

    /**
     *
     * @param type $key
     */
    public function GetServerInfo($key);

    /**
     * @return array POST params
     */
    public function PostParams();

    /**
     * @return array GET params
     */
    public function GetParams();

    /**
     * @return array REQUEST params
     */
    public function RequestParams();

    /**
     * Gets the remote machine's IP
     */
    public function RemoteAddress();

    /**
     * Checks if the user is forwarded from a proxy
     */
    public function IsProxiedRequest();

    /**
     * Block proxy users
     */
    public function blockProxiedUsers();

    /**
     * Checks if the request is from a local machine
     */
    public function IsLocal();
}