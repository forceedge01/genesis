<?php

namespace Application\Components\Router;



use Application\Core\Interfaces\RouterInterface;
use Application\Core\Lib\AppMethods;
use Application\Core\Lib\Debugger;

class Router extends AppMethods implements RouterInterface {

    private
            $lastRoute,
            $lastURL,
            $funcVariables,
            $routePattern,
            $route,
            $params,
            $pattern,
            $ControllerDependencies = array(),
            $ActionDependencies = array();

    public static $LastRoute, $routes = array();

    public function __construct() {
        $this->getComponent('Session')->start();
        $this->funcVariables = array();
        $this->SetPattern()->SetParams();
    }

    /**
     *
     * @return boolean - true on success, false on failure<br />
     * <br />Forwards the request to the appropriate controller once the params are read.
     */
    public function ForwardRequest(){
        Debugger::debugMessage('Forwarding request');

        $this->CheckIfUnderDevelopment();

        if(\Get::Config('Cache.html.enabled'))
            Cache::CheckForCachedFile($this->GetPattern());

        $patternConfig = $this->getPatternConfiguration($this->pattern);

        if(! $patternConfig)
        {
            // Forward to route not found controller
            return false;
        }

        // Save the last route in this param
        $this->lastRoute = $patternConfig['name'];

        // Check http method allowed
        if(isset($patternConfig['Method']))
        {
            $this->CheckRouteMethod($patternConfig['Method']);
        }

        // Check if route variables are set
        if(isset($patternConfig['Requirements']))
        {
            $this->CheckRouteRequirements ($patternConfig['Requirements']);
        }

        // Do some error handling here to see if the right stuff has been provided
        list($bundle, $controller, $action) = explode(':', $patternConfig['Controller']);

        Debugger::debugMessage("Forward request to $bundle:$controller:$action");

        if($bundle)
        {
            $fullBundle = $this->GetBundleFromName($bundle);

            if($fullBundle)
            {
                \Application\AppKernal::getLoader()->LoadBundle($fullBundle);
            }
            else
            {
                $this->ForwardToController('Bundle_Not_Found');
            }
        }

	Debugger::debugMessage("Checking for dependencies");

        $this->CheckDependencies ($bundle, $controller, $action);

        $this->CallAction($this->GetControllerNamespace($bundle, $controller), $action . 'Action', $this->funcVariables);
    }

    private function getPatternConfiguration($toMatchPattern)
    {
        $patternInfo = $this->getPatternInfo($toMatchPattern);

        if(! $patternInfo)
        {
            return false;
        }

        return $this->setControllerVariablesFromPattern($patternInfo);
    }

    private function setControllerVariablesFromPattern($patternInfo)
    {
        // Extract variables pattern call
        if($patternInfo)
        {
            // Pattren matched, extract variables from this pattern
            $routeParams = explode('/', $patternInfo['Pattern']);
            $index = 0;

            foreach($routeParams as $param)
            {
                if(preg_match('(\\{.+\\})', $param))
                {
                    $param = $this->funcVariables[$param] = $this->params[$index];
                }

                $index++;
            }
        }

        return $patternInfo;
    }

    private function getPatternInfo($pattern)
    {
        // Get pattern method call
        foreach(self::$routes as $name => $configuration)
        {
            // If pattern has place holders in it then do the preg match else dont
            if(strpos($configuration['Pattern'], '{') !== false)
            {
                $patternRegex = preg_replace('/{.+}/', '.*', $configuration['Pattern']);

                if(preg_match("#^$patternRegex$#", $pattern))
                {
                    return array_merge($configuration, array('name' => $name));
                }
            }
            else
            {
                if($configuration['Pattern'] == $pattern)
                {
                    return array_merge($configuration, array('name' => $name));
                }
            }

        }
    }

    /**
     *
     * @param type $objectName
     * @param type $action
     * @param array $variable
     */
    private function CallAction($objectName, $action, array $variable = array())
    {
        Debugger::debugMessage("Attempting to call action '$action' of object '$objectName'");

        if(!method_exists($objectName, $action))
        {
            $error = array(
                'Action' => $action,
                'Class' => $objectName,
                'Controller' => $objectName  . ':' . str_replace('Action','',$action),
                'Route' => $this->lastRoute,
                'Backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, Debugger::DEBUG_BACKTRACE_LIMIT)
            );

            $this->ForwardToController('Action_Not_Found', $error);
        }

        if(!empty($variable))
            $this->funcVariables = $variable;

        Debugger::debugMessage("Instantiating object '$objectName'");

	$controller = $this->InstantiateController($objectName);

        Debugger::debugMessage("Calling action '$action' of object '$objectName'");

	// Check for response here
	$response = $this->CallControllerAction($controller, $action);

	// Check response class and process handle
        if(in_array('Application\Core\Interfaces\ResponseInterface', class_implements($response, false))) {
            $response->handle();
        }
        else {
            Debugger::ThrowStaticError('Response returned from controller must implement \'\Application\Core\Interfaces\ResponseInterface\' Interface');
        }

        // Ouput execution time of the whole script
        echo \Application\AppKernal::GetExecutionTime();

        die();
    }

    private function CallControllerAction($controller, $action)
    {
        Debugger::debugMessage("Fetching action dependencies");

        if($this->ActionDependencies) {
            $this->funcVariables = array_merge(
                $this->funcVariables,
                $this->GetComponent('DependencyInjector')->ResolveDependencies($this->ActionDependencies)
            );
        }

        if($this->funcVariables) {
            Debugger::debugMessage("Calling action '$action' with dependencies");

            return call_user_func_array (array($controller, $action) , $this->funcVariables);
        }
        else {
            Debugger::debugMessage("Calling action '$action");

            return call_user_func (array($controller, $action));
        }
    }

    private function InstantiateController($objectName)
    {
        if($this->ControllerDependencies)
        {
            return $this->GetComponent('DependencyInjector')->Inject($objectName, $this->ControllerDependencies);
        }
        else
        {
            return self::InstantiateObject($objectName);
        }
    }

    private function CheckDependencies($bundle, $controller, $action)
    {
        $this->ControllerDependencies = \Get::Config("{$bundle}.{$controller}.Dependencies");
        $this->ActionDependencies = \Get::Config("{$bundle}.{$controller}.{$action}.Dependencies");

        if(! $this->ControllerDependencies)
            $this->ControllerDependencies = array();

        if(! $this->ActionDependencies)
            $this->ActionDependencies = array();

        return $this;
    }

    public function GetPattern(){

        return $this->pattern;
    }

    /**
     *
     * @return Method should set pattern to the value that comes after index.php
     */
    protected function SetPattern(){

        if(isset($_SERVER['PATH_INFO']))
            $pattern = $_SERVER['PATH_INFO'];
        else
            $pattern = '/';

        $this->pattern = $pattern;

        return $this;
    }

    public function GoToHome()
    {
        $this->ForwardTo(\Get::Config('Application.HomeRoute'));
    }

    public function GoToLandingPage()
    {
        $this->ForwardTo(\Get::Config('Application.LandingPageRoute'));
    }

    /**
     * Exploder pattern to an array in $this->params;
     */
    private function SetParams(){

        $this->params = explode('/', $this->pattern);
        return $this;
    }

    public function GetPatternFromUrl($url)
    {
        $pattern = '/^http(s)?:\/\/'.str_replace('/','\\/', getenv('HTTP_HOST').getenv('SCRIPT_NAME')).'/i';
        return preg_replace($pattern, '', $url);
    }

    private function CheckIfUnderDevelopment()
    {
        if(\Get::Config('Application.Environment.UnderDevelopmentPage.State'))
        {
            /*
             * This does not prevent from accessing the page through
             * 1. Load balancers
             * 2. Proxy servers
             * 3. Port forwarded stuff (just consider)
             */
            if(!$this->Variable(getenv('REMOTE_ADDR'))->IsIn(\Get::Config('Application.Environment.UnderDevelopmentPage.ExemptIPs')))
            {
                list($bundle, $controller, $action) = explode(':', \Get::Config('Application.Environment.UnderDevelopmentPage.Controller'));

                $this->CallAction($this->GetControllerNamespace($bundle, $controller), $action . 'Action');
            }
        }

        return $this;
    }

    private function CheckRouteMethod($method)
    {
        $error = false;

        if($this->IsLoopable($method))
        {
            if(strtoupper($method['Type']) != getenv('REQUEST_METHOD'))
            {
                if(isset($method['Message']))
                {
                    if($this->GetRouteFromPattern() != $this->GetRouteFromPattern($this->GetPatternFromUrl($_SERVER['HTTP_REFERER'])))
                    {
                        $this->GetComponent('TemplateHandler')->SetError($method['Message']);
                        $this->getComponent('Router')->ForwardTo($this->lastRoute);
                    }

                    if(isset($method['Fallback']))
                    {
                        $this->GetComponent('TemplateHandler')->SetError($method['Message']);
                        $this->getComponent('Router')->ForwardTo($method['Fallback']);
                    }
                }

                $error = true;
            }
        }
        else if(strtoupper($method) != getenv('REQUEST_METHOD'))
        {
            $error = true;
        }

        if($error)
            $this
                ->SetErrorArgs('Access request denied', 'Router', '0')
                    ->ThrowException();

        return $this;
    }

    private function CheckRouteRequirements($requirements)
    {
        $this->ValidateVariables($requirements);

        return $this;
    }

    private function ValidateVariables($requirement)
    {
        if($this->IsLoopable($requirement))
        {
            foreach($requirement as $key => $pattern)
            {
                if(!preg_match($pattern, $this->funcVariables['{'.$key.'}']))
                {
                    $this
                        ->SetErrorArgs('Route \''.$this->lastRoute.'\' expects variable '.$key.'=\''.$this->funcVariables['{'.$key.'}'].'\' to match \''.$pattern.'\' pattern', 'Route file', 'unknown')
                            ->ThrowError();
                }
            }
        }
    }

    private function GetControllerNamespace($bundle, $controller){

        if($bundle)
            return '\Bundles\\'.$this->GetBundleNameSpace ($bundle).'\Controllers\\' . $controller . 'Controller';

        return '\Application\Struct\Controllers\\'.$controller . 'Controller';
    }

    /**
     *
     * @param type $route
     * @param type $variable
     * @return string The complete route, to be used in templates.
     */
    public function SetRoute($route, array $variable = array()){

        $this->funcVariables = $variable;
        $this->GetRawRoute($route);

        if(!empty($variable))
            $this->ExtractAndReplaceVariable();

        if(strpos($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']) === 0)
            $this->routePattern = $_SERVER['SCRIPT_NAME'] . $this->routePattern;

        return $this->lastURL = $this->routePattern;
    }

    /**
     *
     * @param type $route
     * @return string Gets a route and its details
     */
    public function GetRoute($route, array $variables = array())
    {
        return  $this->SetRoute($route, $variables);
    }

    /**
     *
     * @param type $route
     * @return string gets raw route without any modification.
     */
    private function GetRawRoute($route = null){

        if(! empty($route))
            $this->route = $route;

        if(self::Get($this->route))
        {
            $this->lastRoute = $this->routePattern = self::Get($this->route.'.Pattern');

            return $this->lastRoute;
        }

        $error = array(

            'Route' => $this->route,
            'Pattern' => $this->pattern,
            'Backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
        );

        ob_clean();

        $this->ForwardToController('Route_Not_Found', $error);
    }

    /**
     *
     * @param type $route
     * @return string Gets the controller for a route specified.
     */
    protected function GetController($route){

        if(!empty($route))
            $this->route = $route;

        if(isset(self::$routes[$this->route]))
        {
            $this->routePattern = $this->lastRoute = self::$routes[$this->route]['Pattern'];
            $controller = self::$routes[$this->route]['Controller'];

            unset($_SESSION['routeError']);

            return $controller;
        }

        $error = array(

            'routeName' => $this->route,
            'Pattern' => $this->pattern,
            'Backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)

        );

        $this->ForwardToController('Route_Not_Found', $error);
    }

    /**
     *
     * @param type $route - The route to redirect to
     * @param type $urlQueryString - append this query string to the url to redirect to<br />
     * Redirects to a route.
     */
    public function ForwardTo($route, $urlQueryString = null)
    {
        self::$LastRoute = $this->pattern;
        $this->GetCoreObject('Session')->Set('LastRoute', $route);
        $route = $this->GetRoute($route);

        session_write_close();

        if(ob_get_contents())
            ob_end_flush();

        header('Location: ' . $route . (!empty($urlQueryString) ? '?'.$urlQueryString : '' ));

        // To get testing working with this method
        if(getenv('HTTP_HOST'))
            exit;
    }

    /**
     *
     * @param type $route
     * @param type $variables
     * <br />
     * Forward control from one controller to another without redirecting.
     * Will not check for security bypass
     */
    public function ForwardToController($route, array $variables = array())
    {
        list($bundle, $controller, $action) = explode(':', $this->GetController($route));

        if($bundle)
            \Application\AppKernal::getLoader()->LoadBundle($this->GetBundleFromName($bundle));

        $this->CallAction($this->GetControllerNamespace($bundle, $controller), $action . 'Action', $variables);
    }

    /**
     *
     * @return boolean - true on success, false on failure<br />
     * <br />Extracts multiple variables and sets them up for use in setRoute
     */
    private function ExtractAndReplaceVariable(){

        if(is_array($this->funcVariables))
        {
            foreach($this->funcVariables as $key => $value)
            {
                $this->routePattern = str_replace("\{$key\}", $value, $this->routePattern);
            }
        }

        return $this;
    }

    /**
     *
     * @return boolean - true on success, false on failure<br />
     * <br />Checks wether the page landed on is an exception to session security or not.
     */
    public function IsPageAllowed($patterns){

        $this->SetPattern();

        foreach($patterns as $pattern)
        {
            $pattern = '/'.str_replace('/', '\\/', $pattern).'/i';

            if(preg_match($pattern, $this->pattern))
            {
                return $this;
            }
        }

        return false;
    }

    /**
     *
     * @return boolean<br>
     * Returns the last accessed page route
     */
    protected function LastAccessedPage(){

        if(isset(self::$LastRoute))
            return $this->GetRouteFromPattern(self::$LastRoute);

        return false;
    }

    /**
     *
     * @param type $pattern
     * @return route <br>
     * Returns a route for a given pattern
     */
    protected function GetRouteFromPattern($pattern = null){

        if(!empty($pattern))
            $this->pattern = $pattern;
        else
            $this->SetPattern ();

        if($configuration = $this->getPatternConfiguration($this->pattern))
        {
            $this->route = $configuration['name'];
            $this->routePattern = $pattern;
            unset($_SESSION['routeError']);

            return $this->route;
        }

        $error = array(
            'Pattern' => $this->pattern,
            'Backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
        );

        $this->ForwardToController('404', $error);
    }

    /**
     *
     * @param string $key
     * @param array $routeParams
     */
    public static function Add($key, array $routeParams)
    {
        // Get router here somehow
        self::$routes[$key] = $routeParams;
    }

    /**
     *
     * @param mixed any number of params
     * @return Route variable
     * @example Route('Application','Welcome');
     */
    public static function Get()
    {
        $keys = func_get_args();
        $route = \Get::ProcessGet(self::$routes, $keys);

        if(! $route)
        {
            \Application\Core\Debugger::ThrowStaticError("Could not get route '$keys'");
        }

        return $route;
    }
}
