<?php

namespace Application\Core\Interfaces;



interface Router{

    public function __construct();

    /**
     *
     * @return boolean - true on success, false on failure<br />
     * <br />Forwards the request to the appropriate controller once the params are read.
     */
    public function ForwardRequest();

    /**
     * Get current pattern
     */
    public function GetPattern();

    /**
     * Redirects to the home route defined in the config
     */
    public function RedirectToHome();

    /**
     *
     * @param type $url
     * Gets the pattern defined in the routes file for a url
     */
    public function GetPatternFromUrl($url);

    /**
     *
     * @param type $route
     * @param type $variable
     * @return string The complete route, to be used in templates.
     */
    public function SetRoute($route, array $variable = array());

    /**
     *
     * @param type $route - The route to redirect to
     * @param type $urlQueryString - append this query string to the url to redirect to<br />
     * Redirects to a route.
     */
    public function ForwardTo($route, $urlQueryString = null);

    /**
     *
     * @param type $route
     * @param type $variable
     * <br />
     * Forward control from one controller to another without redirecting.
     */
    public function ForwardToController($route, array $variables = array());

    /**
     *
     * @return Router
     * Returns the router object for further processing
     */
    public function GetRouter();
}