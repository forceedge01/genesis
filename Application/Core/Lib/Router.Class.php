<?php

namespace Application\Core;



use Application\Core\Interfaces\Router as RouterInterface;

class Router extends EventHandler implements RouterInterface{

    private
            $url,
            $lastRoute,
            $lastURL,
            $funcVariables,
            $routePattern,
            $route,
            $params,
            $Router,
            $pattern,
            $ControllerDependencies = array(),
            $ActionDependencies = array();

    public static $Route = array(), $LastRoute;

    public function __construct() {

        $this->url = $_SERVER['PHP_SELF'];
        $this->SetPattern()->SetParams();
    }

    /**
     *
     * @return boolean - true on success, false on failure<br />
     * <br />Forwards the request to the appropriate controller once the params are read.
     */
    public function ForwardRequest(){

        $this->CheckIfUnderDevelopment();

        if(\Get::Config('Cache.html.enabled'))
            Cache::CheckForCachedFile($this->GetPattern());

        $value = $this->funcVariables = array();

        // Should the value contain a regular expression?
        // Render the right controller;
//        $route = $this->GetRouteFromPattern();
//        \Get::Route($route);
//        $routeInfo = self::$Route[$route];
        foreach(self::$Route as $key => $value)
        {
            if($this->ExtractVariable($value['Pattern']) == $this->pattern)
            {
                $this->lastRoute = $key;

                if(isset($value['Method']))
                    $this->CheckRouteMethod($value['Method']);

                if(isset($value['Requirements']))
                    $this->CheckRouteRequirements ($value['Requirements']);

                list($bundle, $controller, $action) = explode(':', $value['Controller']);

                if($bundle)
                    Loader::LoadBundle($this->GetBundleFromName($bundle));

                $this->CheckDependencies ($bundle, $controller, $action);
                $this->CallAction($this->GetControllerNamespace($bundle, $controller), $action . 'Action', $this->funcVariables);
            }
        }

        return false;
    }

    /**
     *
     * @param type $objectName
     * @param type $objectAction
     * @param type $variable<br />
     * Calls an action of a controller.
     */
    private function CallAction($objectName, $action, array $variable = array())
    {
        if(!method_exists($objectName, $action))
        {
            $error = array(
                'Action' => $action,
                'Class' => $objectName,
                'Controller' => $objectName  . ':' . str_replace('Action','',$action),
                'Route' => $this->lastRoute,
                'Backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
            );

            $this->ForwardToController('Action_Not_Found', $error);
        }

        if(!empty($variable))
            $this->funcVariables = $variable;

        $controller = $this->InstantiateController($objectName);

        $this->CallControllerAction($controller, $action);

        // Ouput execution time of the whole script
        echo \Application\Core\AppKernal::GetExecutionTime();

        die();
    }

    private function CallControllerAction($controller, $action)
    {
        if(count($this->ActionDependencies))
            $this->funcVariables = array_merge(
                    $this->funcVariables,
                    $this->GetCoreObject('DependencyInjector')->ResolveDependencies($this->ActionDependencies)
                );

        if(count($this->funcVariables))
        {
            return call_user_func_array (array($controller, $action) , $this->funcVariables);
        }
        else
        {
            return call_user_func (array($controller, $action));
        }
    }

    private function InstantiateController($objectName)
    {
        if(count($this->ControllerDependencies))
        {
            return $this->GetCoreObject('DependencyInjector')->Inject($objectName, $this->ControllerDependencies);
        }
        else
        {
            return $this->InstantiateObject($objectName);
        }
    }

    private function CheckDependencies($bundle, $controller, $action)
    {
        $this->ControllerDependencies = \Get::Config("{$bundle}.{$controller}.Dependencies");
        $this->ActionDependencies = \Get::Config("{$bundle}.{$controller}.{$action}.Dependencies");

        if(!$this->ControllerDependencies)
            $this->ControllerDependencies = array();

        if(!$this->ActionDependencies)
            $this->ActionDependencies = array();

        return $this;
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

        $this->pattern = (isset($_SERVER['PATH_INFO']) ? str_replace('//', '/', $_SERVER['PATH_INFO'] . '/') : '/');

        return $this;

    }

    public function RedirectToHome()
    {
        $this->ForwardTo('Application');
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
        if($method)
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
                            $this->GetCoreObject('Template')->SetError($method['Message'])->ForwardTo($this->lastRoute);
                        }

                        if(isset($method['Fallback']))
                        {
                            $this->GetCoreObject('Template')->SetError($method['Message'])->ForwardTo($method['FallbackRoute']);
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
        }

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

    /**
     *
     * @param type $route
     * @return string extract variables in the url
     */
    private function ExtractVariable($route){

        if(strpos($route,'{'))
        {
            $routeParams = explode('/', $route);
            $index = 0;

            foreach($routeParams as $param)
            {
                if(preg_match('(\\{.*?\\})', $param))
                {
                    if(isset($this->params[$index]))
                    {
                        $param = $this->funcVariables[$param] = $this->params[$index];
                    }
                }

                $routeParams[$index] = $param;
                $index++;
            }

            return implode('/', $routeParams);
        }

        return $route;
    }

    private function GetControllerNamespace($bundle, $controller){

        if($bundle)
            return '\\Bundles\\'.$this->GetBundleNameSpace ($bundle).'\\Controllers\\' . $controller . 'Controller';

        return '\\Application\\Controllers\\'.$controller . 'Controller';
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

        return $this->lastURL = $this->Variable($this->routePattern)->RemoveDoubleOccuranceOf(array('/'))->GetVariableResult();
    }

    /**
     *
     * @param type $route
     * @return string Gets a route and its details
     */
    protected function GetRoute($route)
    {
        return  $this->SetRoute($route);
    }

    /**
     *
     * @param type $route
     * @return string gets raw route without any modification.
     */
    private function GetRawRoute($route = null){

        if(!empty($route))
            $this->route = $route;

        if(\Get::Route($this->route))
        {
            $this->lastRoute = $this->routePattern = \Get::Route($this->route.'.Pattern');

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

        if(isset(self::$Route[$this->route]))
        {
            $this->routePattern = $this->lastRoute = self::$Route[$this->route]['Pattern'];
            $controller = self::$Route[$this->route]['Controller'];

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
    public function ForwardTo($route, $urlQueryString = null){

        self::$LastRoute = $this->pattern;

        $this->GetCoreObject('Session')->Set('LastRoute', $route);

        $route = $this->GetRoute($route);

        session_write_close();

        if(ob_get_contents())
            ob_end_flush();

        header('Location: ' . $route . (!empty($urlQueryString) ? '?'.$urlQueryString : '' ));

        if(getenv('HTTP_HOST'))
            exit;
    }

    /**
     *
     * @param type $route
     * @param type $variable
     * <br />
     * Forward control from one controller to another without redirecting.
     * Will not check for security bypass
     */
    public function ForwardToController($route, array $variables = array())
    {
        list($bundle, $controller, $action) = explode(':', $this->GetController($route));

        if($bundle)
            Loader::LoadBundle($this->GetBundleFromName($bundle));

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

        return true;
    }

    /**
     *
     * @return boolean - true on success, false on failure<br />
     * <br />Checks wether the page landed on is an exception to session security or not.
     */
    protected function IsPageAllowed(){

        $this->SetPattern();

        foreach(\Get::Config('Auth.Security.Bypass') as $pattern)
        {
            $pattern = '/'.str_replace('/', '\\/', $pattern).'/i';

            if(preg_match($pattern, $this->pattern))
            {
                return true;
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

        foreach(self::$Route as $routeKey => $routes)
        {
            if($this->ExtractVariable($routes['Pattern']) == $this->pattern)
            {
                $this->route = $routeKey;
                $this->routePattern = $pattern;
                unset($_SESSION['routeError']);

                return $this->route;
            }
        }

        $error = array(
            'Pattern' => $this->pattern,
            'Backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
        );

        $this->ForwardTo('404', $this->pattern);
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