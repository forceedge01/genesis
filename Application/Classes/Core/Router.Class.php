<?php

namespace Application\Core;



class Router extends Manager{

    private
            $url,
            $lastRoute,
            $lastURL,
            $funcVariable,
            $routePattern,
            $route,
            $params,
            $pageTitle,
            $Router,
            $ObjectArguments = array(),
            $pattern;

    public static $Route = array(), $LastRoute;

    public function __construct() {

        $this->url = $_SERVER['PHP_SELF'];

        $this->SetPattern()->SetParams();
    }

    public function GetPattern(){

        return $this->pattern;
    }

    /**
     *
     * @return boolean - true on success, false on failure<br />
     * <br />Get pattern appended to index.php in url
     */
    protected function SetPattern(){

        $pattern = @$_SERVER['PATH_INFO'] . '/';

        $pattern = str_replace('//', '/', $pattern);

        $this->pattern = @$pattern;

        return $this;

    }

    /**
     * Exploder pattern to an array in $this->params;
     */
    private function SetParams(){

        $this->params = explode('/', $this->pattern);
        return $this;
    }

    /**
     *
     * @return boolean - true on success, false on failure<br />
     * <br />Forwards the request to the appropriate controller once the params are read.
     */
    public function forwardRequest(){

        $value = array();

        //render the right application controller to render template;
        foreach(self::$Route as $key => $value){

            $this->funcVariable = null;

            $value['Pattern'] = $this->extractVariable($value['Pattern']);

            if($value['Pattern'] == $this->pattern){

                if(isset($value['Inject']))
                    $this->ObjectArguments = $value['Inject'];

                $this->lastRoute = $key;

                $controllerAction = explode(':', $value['Controller']);

                $objectName = $this->GetControllerNamespace($controllerAction);

                $objectAction = $controllerAction[2] . 'Action';

                $this->callAction($objectName, $objectAction, $this->funcVariable);
            }
        }

        return false;
    }

    /**
     *
     * @param type $route
     * @return string extract variables in the url
     */
    private function extractVariable($route){

        if(strpos($route,'{'))
        {
            $routeParams = explode('/', $route);

            $pattern = '(\\{.*?\\})';

            $index = 0;

            foreach($routeParams as $param){

                if(preg_match($pattern, $param, $variables)){

                    if(isset($this->params[$index])){

                        $param = $this->params[$index];

                        $this->funcVariable[] = $this->params[$index];

                    }
                }

                $routeParams[$index] = $param;

                $index++;
            }

            $route = $this->reconstructPattern($routeParams);
        }

        return $route;

    }

    private function GetControllerNamespace($controllerAction){

        if($controllerAction[0] == null)
            $namespace = '\\Application\\Core\\Controllers\\';
        else
            $namespace = '\\Application\\Bundles\\'.$controllerAction[0].'\\Controllers\\';

        return $namespace.$controllerAction[1] . 'Controller';
    }

    /**
     *
     * @param type $route
     * @param type $variable
     * @return string The complete route, to be used in templates.
     */
    public function setRoute($route, $variable = null){

        $this->route = $route;

        $this->funcVariable = $variable;

        $this->getRawRoute();

        if(!empty($variable))
            $this->extractAndReplaceVariable();

        $URL = HOST . 'index.php' . $this->routePattern;

        $this->lastURL = $URL;

        return $this->lastURL;
    }

    /**
     *
     * @param type $route
     * @return string Gets a route and its details
     */
    protected function getRoute($route){

        try{

            $route =  $this->setRoute($route);

            return $route;

        }
        catch(Exception $e){

            echo $e->getMessage();
        }

    }

    /**
     *
     * @param type $route
     * @return string gets raw route without any modification.
     */
    private function getRawRoute($route = null){

        if(!empty($route))
            $this->route = $route;

        if(isset(self::$Route[$this->route])){

            $URL = self::$Route[$this->route]['Pattern'];

            $this->lastRoute = $URL;
            $this->routePattern = $URL;

            return $this->lastRoute;
        }

        $error = array(

            'Route' => $this->route,
            'Pattern' => $this->pattern,
            'Backtrace' => debug_backtrace()
        );

        echo 'Route not found: '.$this->route;
        exit;

        $this->forwardToController('Error_Route_Not_Found', $error);
    }

    /**
     *
     * @param type $route
     * @return string Gets the controller for a route specified.
     */
    protected function getController($route){

        if(!empty($route))
            $this->route = $route;

        if(isset(self::$Route[$this->route])){

            $URL = self::$Route[$this->route]['Pattern'];
            $controller = self::$Route[$this->route]['Controller'];

            $this->lastRoute = $URL;
            $this->routePattern = $URL;

            unset($_SESSION['routeError']);

            return $controller;

        }

        $error = array(

            'routeName' => $this->route,
            'Pattern' => $this->pattern,
            'Backtrace' => debug_backtrace()

        );

        $this->forwardToController('Error_Route_Not_Found', $error);
    }

    /**
     *
     * @param type $route - The route to redirect to
     * @param type $urlQueryString - append this query string to the url to redirect to<br />
     * Redirects to a route.
     */
    public function forwardTo($route, $urlQueryString = null){

        self::$LastRoute = $this->pattern;

        $this->GetCoreObject('Session')->Set('LastRoute', $route);

        $route = $this->getRoute($route);

        session_write_close();

        header('Location: ' . $route . (!empty($urlQueryString) ? '?'.$urlQueryString : '' ));

        exit;
    }

    /**
     *
     * @param type $objectName
     * @param type $objectAction
     * @param type $variable<br />
     * Calls an action of a controller.
     */
    private function callAction($objectName, $objectAction, $variable = null){

        if(!empty($variable))
            $this->funcVariable = $variable;

        if(!class_exists($objectName)){

            echo 'Error: '.$objectName;

            $error = array(

                'Class' => $objectName,
                'Controller' => $objectName  . ':' . str_replace('Action','',$objectAction),
                'Route' => $this->lastRoute,
                'Backtrace' => debug_backtrace()

            );

            $this->forwardToController ('Class_Not_Found', $error);

        }

        $controller = new $objectName();

        if(!method_exists($objectName, $objectAction)){

            $error = array(

                'Action' => $objectAction,
                'Class' => $objectName,
                'Controller' => $objectName  . ':' . str_replace('Action','',$objectAction),
                'Route' => $this->lastRoute,
                'Backtrace' => debug_backtrace()

            );

            $this->forwardToController('Action_Not_Found', $error);
        }

        if(\Get::Config('Cache.enabled'))
            Cache::CheckForCachedFile($this->GetPattern());

        if(count($this->ObjectArguments) != 0)
        {
            foreach($this->ObjectArguments as $variable => $object){

                $controller->$variable = new $object;
            }
        }

        if(sizeof($this->funcVariable) != 0){

            call_user_func_array (array($controller, $objectAction) , $this->funcVariable);
        }
        else{

            call_user_func (array($controller, $objectAction));
        }

        unset($controller);

        exit(0);
    }

    /**
     *
     * @param type $route
     * @param type $variable
     * <br />
     * Forward control from one controller to another without redirecting.
     */
    public function forwardToController($route, $variable = null){

        $controller = $this->getController($route);// /Sites/Bla/Bla

        $controllerAction = explode(':', $controller);

        $objectName = $this->GetControllerNamespace($controllerAction);

        $objectAction = $controllerAction[2] . 'Action';

        $this->callAction($objectName, $objectAction, $variable);

    }

    /**
     *
     * @param type $params
     * @return string reconstructs url broken down in extractVariables.
     */
    private function reconstructPattern($params){

        $pattern = function($params){

            $qualified = null;

            foreach($params as $param){

                $qualified .= $param . '/';
            }

            return str_replace('//', '/', $qualified);
        };

        return $pattern($params);
    }

    /**
     *
     * @return boolean - true on success, false on failure<br />
     * <br />Extracts multiple variables and sets them up for use in setRoute
     */
    private function extractAndReplaceVariable(){

        if(is_array($this->funcVariable))
            foreach($this->funcVariable as $key => $value){

                $this->routePattern = str_replace('{'.$key.'}', $value, $this->routePattern);

            }

        return true;
    }

    /**
     *
     * @return boolean - true on success, false on failure<br />
     * <br />Checks wether the page landed on is an exception to session security or not.
     */
    protected function checkExceptionRoutes(){

        $this->SetPattern();

        foreach(\Get::Config('AUTH_BYPASS_ROUTES') as $route){

            $pattern = $this->getRawRoute($route);

            if($this->pattern == $pattern)
                return false;
        }

        return true;
    }

    /**
     *
     * @return boolean<br>
     * Returns the last accessed page route
     */
    protected function lastAccessedPage(){

        if(isset(self::$LastRoute))
            return $this->getRouteFromPattern(self::$LastRoute);
        else
            return true;
    }

    /**
     *
     * @param type $pattern
     * @return route <br>
     * Returns a route for a given pattern
     */
    protected function getRouteFromPattern($pattern = null){

        if(!empty($pattern))
            $this->pattern = $pattern;
        else
            $this->SetPattern ();

        foreach(self::$Route as $routeKey => $routes){

                if($routes['Pattern'] == $this->pattern){

                    $this->route = $routeKey;

                    $this->routePattern = $pattern;

                    unset($_SESSION['routeError']);

                    return $this->route;
                }
        }

        $error = array(

            'Pattern' => $this->pattern,
            'Backtrace' => debug_backtrace()

        );

        $this->forwardToController('Error_Route_Not_Found', $error);
    }

    /**
     *
     * @param type $title
     * @return \Router <br>
     * Sets the page title
     */
    public function SetPageTitle($title){

        $this->pageTitle = $title;

        return $this;
    }

    /**
     *
     * @return string pageTitle
     * Returns the page title of the current page
     */
    public function GetPageTitle(){

        return $this->pageTitle;
    }

    /**
     *
     * @return Router
     * Returns the router object for further processing
     */
    public function GetRouter(){

        return $this->Router;
    }
}