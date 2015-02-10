<?php

namespace Application\Core\Interfaces;



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
    public function isPost();

    /**
     * @param String The element to check (optional)
     * @return boolean
     * Returns true if the request is an get request
     */
    public function isGet();

    /**
     * @return string
     * Return a query string param submitted to the request url
     */
    public function getQueryStringParam($key);

    /**
     * @return string
     * Return a post parameter submitted to the request url
     */
    public function getPostParam($key);

    /**
     * @param string $Name
     * @return Gets a cookie value
     */
    public function getCookie($Name);

    /**
     * @param string $Name
     * @return Checks if a cookie exists
     */
    public function hasCookie($Name);

    /**
     * Checks if the user is forwarded from a proxy
     */
    public function isProxiedRequest();

    /**
     * Block proxy users
     */
    public function blockProxiedUsers();

    /**
     * Checks if the request is from a local machine
     */
    public function isLocal();
}
